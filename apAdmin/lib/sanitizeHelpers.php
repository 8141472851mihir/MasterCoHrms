<?php

/**
 * Input sanitize / SQL escape helpers for report filters, action IDs, downloads, and DataTables.
 * Used by dao via: use SanitizeHelpers;
 */
trait SanitizeHelpers
{
    function validateDate($date, $format = 'Y-m-d')
    {
        $da = DateTime::createFromFormat($format, $date);
        return $da && $da->format($format) === $date;
    }

    /**
     * Sanitize comma/hyphen-separated id list from report filters (bId, dId, uId, etc.) for SQL FIND_IN_SET.
     */
    function sanitizeReportFilterIds($input)
    {
        if ($input === '' || $input === null) {
            return '';
        }
        if (is_array($input)) {
            $input = implode('-', $input);
        }
        $ids = array_filter(array_map('intval', explode(',', str_replace('-', ',', $input))));
        return !empty($ids) ? implode(',', $ids) : '';
    }

    function sanitizeReportFilterStatusKeys($input)
    {
        if ($input === '' || $input === null) {
            return '';
        }

        if (is_array($input)) {
            $input = implode('-', $input);
        }

        $ids = array_filter(
            array_map('intval', explode(',', str_replace('-', ',', $input))),
            function ($value) {
                return $value >= 0; // Allow 0 and positive integers
            }
        );

        return !empty($ids) ? implode(',', $ids) : '';
    }

    /**
     * Sanitize a single report filter id (first id when comma/hyphen list is passed).
     */
    function sanitizeReportFilterIdAsInt($input, $default = 0)
    {
        $ids = $this->sanitizeReportFilterIds($input);
        return ($ids !== '') ? (int) explode(',', $ids)[0] : $default;
    }

    /**
     * Sanitize id list from POST action endpoints (bulk delete, status toggle).
     */
    function sanitizeActionIds($input)
    {
        if ($input === null || $input === '') {
            return array();
        }
        if (!is_array($input)) {
            $id = (int) $input;
            return $id > 0 ? array($id) : array();
        }
        return array_values(array_filter(array_map('intval', $input), function ($id) {
            return $id > 0;
        }));
    }

    /**
     * Sanitize a single id from POST action endpoints.
     */
    function sanitizeActionIdAsInt($input, $default = 0)
    {
        if (is_array($input)) {
            $ids = $this->sanitizeActionIds($input);
            return !empty($ids) ? $ids[0] : $default;
        }
        $id = (int) $input;
        return $id > 0 ? $id : $default;
    }

    /**
     * Allowlist relative admin redirect paths (blocks open redirects).
     */
    function adminSafeLocalRedirectPath($input, $default = 'welcome')
    {
        $default = trim((string) $default);
        if ($default === '') {
            $default = 'welcome';
        }

        $input = trim((string) $input);
        if ($input === '') {
            return $default;
        }

        if (preg_match('#^\s*([a-z][a-z0-9+.-]*:|//)#i', $input)) {
            return $default;
        }

        $input = str_replace('\\', '/', $input);
        if (strpos($input, '..') !== false) {
            return $default;
        }

        $input = ltrim($input, '/');
        if (!preg_match('#^[a-zA-Z0-9_./?=&%-]+$#', $input)) {
            return $default;
        }

        return $input;
    }

    /**
     * Feedback list filters carried via GET/POST (no session/cookie).
     */
    function feedbackListFilterParams($source = null)
    {
        if ($source === null) {
            $source = array_merge($_GET, $_POST);
        }
        $keys = array('Status', 'platform_filter', 'module_type_filter', 'created_by_filter', 'project_type_filter', 'support_person_filter', 'tab');
        $params = array();
        foreach ($keys as $key) {
            if (!isset($source[$key]) || is_array($source[$key])) {
                continue;
            }
            $val = trim((string) $source[$key]);
            if ($val === '') {
                continue;
            }
            $pattern = $key === 'support_person_filter'
                ? '/^[A-Za-z0-9_ .\'-]+$/'
                : '/^[A-Za-z0-9_.-]+$/';
            if (!preg_match($pattern, $val)) {
                continue;
            }
            $params[$key] = $val;
        }
        return $params;
    }

    function feedbackListQueryString($source = null)
    {
        return http_build_query($this->feedbackListFilterParams($source));
    }

    function feedbackListQuerySuffix($source = null)
    {
        $qs = $this->feedbackListQueryString($source);
        return $qs === '' ? '' : ('?' . $qs);
    }

    function feedbackListHiddenInputs($source = null)
    {
        $html = '';
        foreach ($this->feedbackListFilterParams($source) as $key => $val) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '">';
        }
        return $html;
    }

    function feedbackListRedirectPath($previousUrl = '', $default = 'feedback')
    {
        $safe = $this->adminSafeLocalRedirectPath($previousUrl, $default);
        $pathOnly = explode('?', $safe, 2)[0];
        if ($pathOnly === 'feedback') {
            parse_str((string) parse_url($safe, PHP_URL_QUERY), $existing);
            $merged = array_merge(is_array($existing) ? $existing : array(), $this->feedbackListFilterParams());
            $qs = http_build_query($merged);
            return $pathOnly . ($qs !== '' ? ('?' . $qs) : '');
        }
        return $safe;
    }

    /**
     * Resolve a download path under allowed project directories (blocks path traversal).
     */
    function adminSafeDownloadPath($input, array $allowedRelativeDirs = array())
    {
        $input = trim(str_replace('\\', '/', (string) $input));
        if ($input === '' || preg_match('#\.\.|^[a-z]+:#i#', $input)) {
            return '';
        }

        if (empty($allowedRelativeDirs)) {
            $allowedRelativeDirs = array('img', 'GovernmentComplianceReports', 'apAdmin');
        }

        $projectRoot = realpath(dirname(__DIR__, 2));
        if ($projectRoot === false) {
            return '';
        }

        $candidates = array();
        $apAdminDir = realpath(dirname(__DIR__));
        if ($apAdminDir !== false) {
            $candidates[] = $apAdminDir . '/' . ltrim($input, '/');
        }
        $candidates[] = $projectRoot . '/' . ltrim(preg_replace('#^\.\./#', '', $input), '/');

        $allowedRoots = array();
        foreach ($allowedRelativeDirs as $dir) {
            $dir = trim(str_replace('\\', '/', (string) $dir), '/');
            if ($dir === '' || strpos($dir, '..') !== false) {
                continue;
            }
            $real = realpath($projectRoot . '/' . $dir);
            if ($real !== false) {
                $allowedRoots[] = $real;
            }
        }

        foreach ($candidates as $candidatePath) {
            $resolved = realpath($candidatePath);
            if ($resolved === false || !is_file($resolved)) {
                continue;
            }
            foreach ($allowedRoots as $allowedRoot) {
                if (strpos($resolved, $allowedRoot) === 0) {
                    return $resolved;
                }
            }
        }

        return '';
    }

    /**
     * Sanitize a download filename for Content-Disposition header.
     */
    function adminSafeDownloadFilename($input, $default = 'report')
    {
        $name = preg_replace('/[^a-zA-Z0-9._-]+/', '_', (string) $input);
        $name = trim($name, '._-');
        return $name !== '' ? $name : $default;
    }

    /**
     * Sanitize a report filter date (Y-m-d). Returns $default when input is empty or invalid.
     */
    function sanitizeReportFilterDate($input, $default = null)
    {
        if ($default === null) {
            $default = date('Y-m-d');
        }
        if ($input === '' || $input === null) {
            return $default;
        }
        $input = trim((string) $input);
        if ($this->validateDate($input, 'Y-m-d')) {
            return $input;
        }
        return $default;
    }

    /**
     * Sanitize year filter (e.g. laYear, year).
     */
    function sanitizeReportFilterYear($input, $default = null)
    {
        if ($default === null) {
            $default = (int) date('Y');
        }
        if ($input === '' || $input === null) {
            return (int) $default;
        }
        $year = (int) $input;
        return ($year >= 1970 && $year <= 2100) ? $year : (int) $default;
    }

    /**
     * Sanitize fiscal year filter (e.g. 2026-2027 for Apr–Mar financial year).
     */
    function sanitizeReportFilterFiscalYear($input, $default = null)
    {
        if ($default === null || $default === '') {
            if (date('m') >= 4) {
                $default = date('Y') . '-' . date('Y', strtotime('+1 year'));
            } else {
                $default = date('Y', strtotime('-1 year')) . '-' . date('Y');
            }
        }
        $default = (string) $default;
        if ($input === '' || $input === null) {
            return $default;
        }
        $input = trim((string) $input);
        if (preg_match('/^(\d{4})-(\d{4})$/', $input, $m)) {
            $start = (int) $m[1];
            $end = (int) $m[2];
            if ($end === $start + 1 && $start >= 1970 && $end <= 2101) {
                return $input;
            }
        }
        if (preg_match('/^(\d{4})-(\d{4})$/', $default, $dm)) {
            $start = (int) $dm[1];
            $end = (int) $dm[2];
            if ($end === $start + 1 && $start >= 1970 && $end <= 2101) {
                return $default;
            }
        }
        return $default;
    }

    /**
     * Sanitize month filter as zero-padded MM (01-12), e.g. month_year on attendance reports.
     */
    function sanitizeReportFilterMonth($input, $default = null)
    {
        if ($default === null || $default === '') {
            $default = date('m');
        }
        if ($input === '' || $input === null) {
            $month = (int) $default;
        } else {
            $month = (int) $input;
        }
        if ($month >= 1 && $month <= 12) {
            return str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        }
        $defaultMonth = (int) $default;
        if ($defaultMonth >= 1 && $defaultMonth <= 12) {
            return str_pad((string) $defaultMonth, 2, '0', STR_PAD_LEFT);
        }
        return date('m');
    }

    /**
     * Sanitize month filter as integer (1-12, or 0 when $allowZero and "all months" is valid).
     */
    function sanitizeReportFilterMonthAsInt($input, $default = null, $allowZero = false)
    {
        if ($default === null) {
            $default = (int) date('n');
        }
        if ($input === '' || $input === null) {
            return (int) $default;
        }
        $month = (int) $input;
        if ($allowZero && $month === 0) {
            return 0;
        }
        return ($month >= 1 && $month <= 12) ? $month : (int) $default;
    }

    /**
     * Sanitize month_year filter in Y-m format (e.g. 2026-03 for salary/target reports).
     */
    function sanitizeReportFilterMonthYear($input, $default = null)
    {
        if ($default === null || $default === '') {
            $default = date('Y-m');
        }
        if ($input === '' || $input === null) {
            return (string) $default;
        }
        $input = trim((string) $input);
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $input, $m)) {
            $year = (int) $m[1];
            $month = (int) $m[2];
            if ($year >= 1970 && $year <= 2100 && $month >= 1 && $month <= 12) {
                return $year . '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT);
            }
        }
        if ($this->validateDate($input, 'Y-m-d')) {
            return date('Y-m', strtotime($input));
        }
        return (string) $default;
    }

    /**
     * Sanitize sister-company filter (scId): 0 = all, -1 = main society, or positive id.
     */
    function sanitizeReportFilterScId($input, $default = 0)
    {
        if ($input === '' || $input === null) {
            return (int) $default;
        }
        $id = (int) $input;
        if ($id === -1 || $id >= 0) {
            return $id;
        }
        return (int) $default;
    }

    /**
     * Parse payroll filter "month-year" (e.g. 3-2026). Returns ['month'=>int,'year'=>int] or false.
     */
    function sanitizePayrollFilter($input)
    {
        if ($input === '' || $input === null) {
            return false;
        }
        $input = trim((string) $input);
        if (!preg_match('/^(\d{1,2})-(\d{4})$/', $input, $m)) {
            return false;
        }
        $month = (int) $m[1];
        $year = (int) $m[2];
        if ($month < 1 || $month > 12 || $year < 1970 || $year > 2100) {
            return false;
        }
        return array('month' => $month, 'year' => $year);
    }

    /**
     * Government compliance report output type (0 = on-screen, 1 = PDF download).
     */
    function sanitizeReportOutputType($input, $default = 0)
    {
        $allowed = array(0, 1);
        if ($input === '' || $input === null) {
            return (int) $default;
        }
        $val = (int) $input;
        return in_array($val, $allowed, true) ? $val : (int) $default;
    }

    /**
     * Escape a string for safe use inside SQL quoted literals.
     */
    function escapeSqlString($input)
    {
        if ($input === null || $input === '') {
            return '';
        }
        return $this->conn->real_escape_string((string) $input);
    }

    /**
     * Validate master_menu.menu_link page name (alphanumeric, underscore, dot, hyphen).
     */
    function sanitizeMenuLinkFilter($input)
    {
        $input = trim((string) $input);
        if ($input === '' || !preg_match('/^[a-zA-Z0-9_.-]+$/', $input)) {
            return '';
        }
        return $input;
    }

    /**
     * Sanitize assets maintenance type dropdown (mt).
     */
    function sanitizeMaintenanceTypeFilter($input, $default = 'all')
    {
        $allowed = array('all', '0', '1', '2', '3', '4', '5', '6');
        $input = (string) $input;
        return in_array($input, $allowed, true) ? $input : $default;
    }

    /**
     * Validate UUID string (hex + hyphens). Returns trimmed UUID or false when invalid.
     */
    function sanitizeUuid($input)
    {
        $input = trim((string) $input);
        if ($input === '' || !preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $input)) {
            return false;
        }
        return $input;
    }

    /**
     * Escape value for SQL LIKE patterns.
     */
    function escapeSqlLike($input)
    {
        $escaped = $this->escapeSqlString($input);
        return str_replace(array('%', '_'), array('\\%', '\\_'), $escaped);
    }

    /**
     * Whitelist DataTables ORDER BY column from index map.
     */
    function sanitizeDatatableOrderColumn($columnIndex, array $allowedColumns, $default = 0)
    {
        $columnIndex = (int) $columnIndex;
        if (isset($allowedColumns[$columnIndex])) {
            return $allowedColumns[$columnIndex];
        }
        return $allowedColumns[(int) $default];
    }

    /**
     * Sanitize DataTables ORDER BY direction.
     */
    function sanitizeDatatableOrderDir($dir)
    {
        return (strtolower((string) $dir) === 'desc') ? 'DESC' : 'ASC';
    }

    /**
     * Sanitize access_type id for app_access_master queries.
     */
    function sanitizeAccessTypeId($input, $default = 0)
    {
        return $this->sanitizeReportFilterIdAsInt($input, $default);
    }
}
