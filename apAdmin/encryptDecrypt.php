<style>
  .enc-page .card-body {
    padding: 10px 14px 12px;
  }

  .enc-page .page-title {
    font-size: 18px;
  }

  .enc-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 16px;
    background: linear-gradient(180deg, #fafbfc 0%, #f3f5f7 100%);
    border: 1px solid #e3e6ea;
    border-radius: 6px;
    padding: 8px 10px;
    margin-bottom: 10px;
  }

  .enc-toolbar-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
  }

  .enc-toolbar-group + .enc-toolbar-group {
    padding-left: 14px;
    border-left: 1px solid #dde1e6;
  }

  .enc-toolbar-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: #8a939e;
    margin-right: 4px;
  }

  .enc-chips {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 4px;
    background: #fff;
    border: 1px solid #e3e6ea;
    border-radius: 6px;
    padding: 3px;
  }

  .enc-chips .enc-chip {
    margin: 0;
  }

  .enc-chips .enc-chip input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .enc-chips .enc-chip label {
    display: inline-block;
    margin: 0;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    color: #5c6770;
    cursor: pointer;
    line-height: 1.3;
    transition: background 0.15s ease, color 0.15s ease;
  }

  .enc-chips .enc-chip label:hover {
    background: #f0f3f6;
    color: #2f3a44;
  }

  .enc-chips .enc-chip input:checked + label {
    background: #1e88e5;
    color: #fff;
    box-shadow: 0 1px 2px rgba(30, 136, 229, 0.35);
  }

  #keyTable {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    width: 100%;
    margin-top: 2px;
    padding-top: 8px;
    border-top: 1px dashed #dde1e6;
  }

  #keyTable .enc-key-field {
    flex: 1 1 180px;
  }

  #keyTable label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #8a939e;
    margin-bottom: 3px;
  }

  .enc-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
  }

  @media (max-width: 991px) {
    .enc-grid {
      grid-template-columns: 1fr;
    }

    .enc-toolbar-group + .enc-toolbar-group {
      padding-left: 0;
      border-left: 0;
    }
  }

  .enc-section {
    border: 1px solid #e3e6ea;
    border-radius: 6px;
    background: #fff;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.03);
  }

  .enc-section.is-accent {
    border-color: #cfe2f5;
  }

  .enc-section.is-accent .enc-section-header {
    background: #eef6fc;
    color: #1565c0;
    border-bottom-color: #d6e8f7;
  }

  .enc-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7f8fa;
    border-bottom: 1px solid #e8ebef;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #3d4751;
  }

  .enc-section-header i {
    opacity: 0.7;
  }

  .enc-section-body {
    padding: 8px 10px;
  }

  .enc-section-body .form-control,
  .enc-section-body .table .form-control {
    font-size: 13px;
    padding: 5px 8px;
    height: auto;
    border-color: #d8dde3;
    border-radius: 4px;
  }

  .enc-section-body .form-control:focus {
    border-color: #1e88e5;
    box-shadow: 0 0 0 2px rgba(30, 136, 229, 0.15);
  }

  .enc-section-body .table {
    margin-bottom: 0;
    font-size: 12px;
  }

  .enc-section-body .table thead th {
    background: #fafbfc;
    border-color: #e8ebef;
    color: #6b7580;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .enc-section-body .table th,
  .enc-section-body .table td {
    padding: 5px 6px;
    vertical-align: middle;
    border-color: #e8ebef;
  }

  .enc-paste-row {
    display: flex;
    gap: 8px;
    align-items: stretch;
  }

  .enc-paste-row textarea {
    flex: 1;
    min-height: 36px;
    max-height: 72px;
    resize: vertical;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
  }

  .enc-paste-row .btn {
    white-space: nowrap;
    min-width: 72px;
    font-size: 12px;
    font-weight: 600;
    padding: 0 12px;
  }

  .enc-bulk-area {
    min-height: 88px;
    height: 88px;
    resize: vertical;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
  }

  .enc-action-inline {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 2px;
  }

  .enc-action-inline .btn {
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 5px;
  }

  .enc-result-box {
    background: #f8fafb;
    border: 1px solid #e3e6ea;
    border-radius: 6px;
    padding: 10px 12px;
    margin-top: 8px;
    word-break: break-all;
  }

  .enc-result-box .enc-result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
  }

  .enc-result-box .enc-result-header strong {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #6b7580;
  }

  .enc-result-content {
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
    line-height: 1.45;
    min-height: 200px;
    max-height: 320px;
    overflow-y: auto;
    overflow-x: auto;
    cursor: pointer;
    color: #2f3a44;
  }

  .enc-result-content .enc-json-preview {
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
    white-space: pre-wrap;
    font-family: Consolas, Monaco, monospace;
    font-size: 13px;
    line-height: 1.5;
    color: #1f2933;
    overflow-wrap: anywhere;
  }

  .enc-result-content .enc-json-key {
    color: #0b6bcb;
  }

  .enc-result-content .enc-json-string {
    color: #0a7a3e;
  }

  .enc-result-content .enc-json-number {
    color: #c2410c;
  }

  .enc-result-content .enc-json-bool,
  .enc-result-content .enc-json-null {
    color: #7c3aed;
  }

  .form-width {
    width: 70px;
    padding: 0;
    text-align: center;
  }

  .enc-compact-textarea {
    min-height: 72px;
    max-height: 110px;
    resize: vertical;
    font-size: 13px;
  }

  .enc-equal {
    height: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 0;
  }

  .enc-equal .enc-section-body {
    flex: 1;
  }

  .enc-equal .enc-section-body.p-0 {
    display: flex;
    flex-direction: column;
  }

  .enc-equal .table-responsive {
    flex: 1;
  }
</style>
<div class="content-wrapper enc-page">
  <div class="container-fluid">
    <div class="row pt-1 pb-1">
      <div class="col-12">
        <h4 class="page-title mb-0">Encrypt/Decrypt</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <form id="EncryptDecryptForm">
              <input type="hidden" name="action1" value="encryptData" id="encryptData" required />

              <div class="enc-toolbar">
                <div class="enc-toolbar-group">
                  <span class="enc-toolbar-label">Key</span>
                  <div class="enc-chips">
                    <div class="enc-chip">
                      <input class="defaultKey" type="radio" value="defaultKey" id="defaultKey" name="checkbox2" checked>
                      <label for="defaultKey">Default</label>
                    </div>
                    <div class="enc-chip">
                      <input class="manualKey" type="radio" value="manualKey" id="manualKey" name="checkbox2">
                      <label for="manualKey">Manual</label>
                    </div>
                  </div>
                </div>
                <div class="enc-toolbar-group">
                  <span class="enc-toolbar-label">Mode</span>
                  <div class="enc-chips">
                    <div class="enc-chip">
                      <input class="encrypt" type="radio" value="encrypt" id="encrypt" name="checkbox" checked>
                      <label for="encrypt">Encryption</label>
                    </div>
                    <div class="enc-chip">
                      <input class="decrypt" type="radio" value="decrypt" id="decrypt" name="checkbox">
                      <label for="decrypt">Decryption</label>
                    </div>
                    <div class="enc-chip">
                      <input class="encrypt" type="radio" value="encrypt" id="text-encrypt" name="checkbox">
                      <label for="text-encrypt">Text Encrypt</label>
                    </div>
                    <div class="enc-chip">
                      <input class="decrypt" type="radio" value="decrypt" id="text-decrypt" name="checkbox">
                      <label for="text-decrypt">Text Decrypt</label>
                    </div>
                  </div>
                </div>
                <div class="enc-toolbar-group" id="compressToolbarGroup">
                  <span class="enc-toolbar-label">Payload</span>
                  <div class="enc-chips">
                    <div class="enc-chip">
                      <input type="radio" value="0" id="uncompressed" name="compress" checked>
                      <label for="uncompressed">Uncompressed</label>
                    </div>
                    <div class="enc-chip">
                      <input type="radio" value="1" id="compressed" name="compress">
                      <label for="compressed">Compressed</label>
                    </div>
                  </div>
                </div>
                <div class="d-none w-100" id="keyTable">
                  <div class="enc-key-field">
                    <label for="enc_key">Encryption Key</label>
                    <input type="text" class="form-control form-control-sm" name="enc_key" id="enc_key" required />
                  </div>
                  <div class="enc-key-field">
                    <label for="enc_iv">IV</label>
                    <input type="text" class="form-control form-control-sm" name="enc_iv" id="enc_iv" />
                  </div>
                </div>
              </div>

              <div class="d-none" id="encryptform">
                <div class="enc-section is-accent">
                  <div class="enc-section-header">
                    <span><i class="fa fa-clipboard mr-1"></i> Paste Encrypted or JSON to Edit</span>
                  </div>
                  <div class="enc-section-body">
                    <div class="enc-paste-row">
                      <textarea rows="1" class="form-control" id="paste_encrypted_input"
                        name="paste_encrypted_input"
                        placeholder='Paste encrypted string or JSON e.g. {"ssad": "asdas"} — auto-loads on paste'></textarea>
                      <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadEncryptedForEditing()">Load</button>
                    </div>
                  </div>
                </div>

                <div class="enc-grid">
                  <div class="enc-section enc-equal">
                    <div class="enc-section-header">
                      <span>Key / Value</span>
                      <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" onclick="addRow()">+ Add</button>
                    </div>
                    <div class="enc-section-body p-0">
                      <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                          <thead>
                            <tr>
                              <th>Key</th>
                              <th>Value</th>
                              <th class="form-width"></th>
                            </tr>
                          </thead>
                          <tbody id="tableBody">
                            <tr>
                              <td><input type="text" class="form-control changetext" onchange="updateBulkInsert()"
                                  name="key[]" required /></td>
                              <td><input type="text" class="form-control changetext" onchange="updateBulkInsert()"
                                  name="value[]" required /></td>
                              <td class="form-width"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="enc-section enc-equal">
                    <div class="enc-section-header"><span>Bulk Upload</span></div>
                    <div class="enc-section-body">
                      <textarea class="form-control enc-bulk-area" name="multipleInsert_input"
                        placeholder="key: value" required></textarea>
                    </div>
                  </div>
                </div>

                <div class="enc-action-inline">
                  <button type="button" class="btn btn-primary btn-sm" onclick="generateEncryptedString()">
                    <i class="fa fa-lock mr-1"></i> Generate Encrypted String
                  </button>
                </div>

                <div class="row enc-hidden-div d-none no-gutters">
                  <div class="col-lg-6 pr-lg-1">
                    <div class="enc-result-box">
                      <div class="enc-result-header">
                        <strong>Encrypted</strong>
                        <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("encryptedResult")'>
                          <i class="fa fa-copy"></i> Copy
                        </button>
                      </div>
                      <div class="enc-result-content" id="encryptedResult" onClick="clickable('encryptedResult')"></div>
                    </div>
                  </div>
                  <div class="col-lg-6 pl-lg-1">
                    <div class="enc-result-box">
                      <div class="enc-result-header">
                        <strong>Preview</strong>
                        <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("decryptedResult")'>
                          <i class="fa fa-copy"></i> Copy
                        </button>
                      </div>
                      <div class="enc-result-content" id="decryptedResult" onClick="clickable('decryptedResult')"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-none" id="decryptform">
                <input type="hidden" name="action2" value="decryptData" id="decryptData" required />
                <div class="enc-section">
                  <div class="enc-section-header"><span>Encrypted String</span></div>
                  <div class="enc-section-body">
                    <textarea rows="3" class="form-control enc-compact-textarea" name="encrypted_input"
                      placeholder="Paste encrypted string" required></textarea>
                  </div>
                </div>
                <div class="enc-action-inline">
                  <button type="button" class="btn btn-primary btn-sm" onclick="generateDecryptedString()">
                    <i class="fa fa-unlock mr-1"></i> Generate Decrypted String
                  </button>
                </div>
                <div class="dec-hidden-div d-none">
                  <div class="enc-result-box">
                    <div class="enc-result-header">
                      <strong>Decrypted</strong>
                      <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("decryptedResultNew")'>
                        <i class="fa fa-copy"></i> Copy
                      </button>
                    </div>
                    <div class="enc-result-content" id="decryptedResultNew" onClick="clickable('decryptedResultNew')"></div>
                  </div>
                </div>
              </div>

              <div class="d-none" id="text-encryptform">
                <div class="enc-section">
                  <div class="enc-section-header"><span>Plain Text</span></div>
                  <div class="enc-section-body">
                    <textarea rows="3" class="form-control enc-compact-textarea" name="multipleInsert_input"
                      placeholder="Enter text to encrypt" required></textarea>
                  </div>
                </div>
                <div class="enc-action-inline">
                  <button type="button" class="btn btn-primary btn-sm" onclick="generateTextEncryptedString()">
                    <i class="fa fa-lock mr-1"></i> Generate Encrypted String
                  </button>
                </div>
                <div class="row enc-hidden-div d-none no-gutters">
                  <div class="col-lg-6 pr-lg-1">
                    <div class="enc-result-box">
                      <div class="enc-result-header">
                        <strong>Encrypted</strong>
                        <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("encryptedTextResult")'>
                          <i class="fa fa-copy"></i> Copy
                        </button>
                      </div>
                      <div class="enc-result-content" id="encryptedTextResult" onClick="clickable('encryptedTextResult')"></div>
                    </div>
                  </div>
                  <div class="col-lg-6 pl-lg-1">
                    <div class="enc-result-box">
                      <div class="enc-result-header">
                        <strong>Preview</strong>
                        <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("decryptedTextResult")'>
                          <i class="fa fa-copy"></i> Copy
                        </button>
                      </div>
                      <div class="enc-result-content" id="decryptedTextResult" onClick="clickable('decryptedTextResult')"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-none" id="text-decryptform">
                <input type="hidden" name="action3" value="textDecryptData" id="textDecryptData" required />
                <div class="enc-section">
                  <div class="enc-section-header"><span>Encrypted Text</span></div>
                  <div class="enc-section-body">
                    <textarea rows="3" class="form-control enc-compact-textarea" name="text_decrypt_input"
                      placeholder="Paste encrypted text" required></textarea>
                  </div>
                </div>
                <div class="enc-action-inline">
                  <button type="button" class="btn btn-primary btn-sm" onclick="generateTextDecryptedString()">
                    <i class="fa fa-unlock mr-1"></i> Generate Decrypted String
                  </button>
                </div>
                <div class="dec-hidden-div d-none">
                  <div class="enc-result-box">
                    <div class="enc-result-header">
                      <strong>Decrypted</strong>
                      <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" onclick='clickable("decryptedTextResultNew")'>
                        <i class="fa fa-copy"></i> Copy
                      </button>
                    </div>
                    <div class="enc-result-content" id="decryptedTextResultNew" onClick="clickable('decryptedTextResultNew')"></div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var element1 = document.getElementById("encryptform");
    var element2 = document.getElementById("decryptform");
    var element3 = document.getElementById("text-encryptform");
    var element4 = document.getElementById("text-decryptform");
    var compressGroup = document.getElementById("compressToolbarGroup");
    var encryptbutton = document.getElementById("encrypt");

    function setCompressVisible(show) {
      if (!compressGroup) return;
      if (show) {
        compressGroup.classList.remove("d-none");
      } else {
        compressGroup.classList.add("d-none");
      }
    }

    if (encryptbutton.value == "encrypt") {
      element1.classList.remove("d-none");
      element2.classList.add("d-none");
      element3.classList.add("d-none");
      element4.classList.add("d-none");
      setCompressVisible(true);
    }
    document.getElementById("decrypt").addEventListener("change", function () {
      element1.classList.add("d-none");
      element2.classList.remove("d-none");
      element3.classList.add("d-none");
      element4.classList.add("d-none");
      setCompressVisible(false);
    });
    document.getElementById("text-encrypt").addEventListener("change", function() {
      element1.classList.add("d-none");
      element2.classList.add("d-none");
      element3.classList.remove("d-none");
      element4.classList.add("d-none");
      setCompressVisible(false);
    });
    document.getElementById("encrypt").addEventListener("change", function () {
      element1.classList.remove("d-none");
      element2.classList.add("d-none");
      element3.classList.add("d-none");
      element4.classList.add("d-none");
      setCompressVisible(true);
    });
    document.getElementById("text-decrypt").addEventListener("change", function() {
      element1.classList.add("d-none");
      element2.classList.add("d-none");
      element3.classList.add("d-none");
      element4.classList.remove("d-none");
      setCompressVisible(false);
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    var element1 = document.getElementById("keyTable");
    var defaultKeybutton = document.getElementById("defaultKey");
    document.getElementById("defaultKey").addEventListener("change", function () {
      element1.classList.add("d-none");

    });
    document.getElementById("manualKey").addEventListener("change", function () {
      element1.classList.remove("d-none");
    });
  });
  function updateBulkInsert() {
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr');
    const textarea = document.querySelector('#encryptform textarea[name="multipleInsert_input"]');
    const data = Array.from(rows)
      .map(row => {
        const key = row.querySelector('input[name="key[]"]').value.trim();
        const value = row.querySelector('input[name="value[]"]').value.trim();
        return key || value ? `${key}: ${value}` : null;
      })
      .filter(Boolean);
    textarea.value = data.join('\n');
  }
  function removeRow(event, button) {
    const row = button.parentNode.parentNode;
    row.remove();
    updateBulkInsert();
    const textarea = document.querySelector('#encryptform textarea[name="multipleInsert_input"]');
    const tableBody = document.getElementById('tableBody');
    const inputText = textarea.value;
    const lines = inputText.split('\n');
    const numberoflines = lines.length;
    if (numberoflines == 1 && inputText == '') {
      addRow();
    }
  }
  function addRow() {
    const tableBody = document.getElementById('tableBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" class="form-control changetext" onchange="updateBulkInsert()" name="key[]" required/></td>
        <td><input type="text" class="form-control changetext" onchange="updateBulkInsert()" name="value[]" required/></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(event, this)">Remove</button></td>
  `;
    tableBody.appendChild(newRow);
    updateBulkInsert();
  }
  document.addEventListener('DOMContentLoaded', function () {
    const inputBoxes = document.querySelectorAll('input.changetext');
    inputBoxes.forEach(inputBox => {
      inputBox.addEventListener('change', function () {
        updateBulkInsert();
      });
    });
  });
  document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.querySelector('#encryptform textarea[name="multipleInsert_input"]');
    const tableBody = document.getElementById('tableBody');
    textarea.addEventListener('change', function () {
      const inputText = textarea.value;
      tableBody.innerHTML = '';
      const lines = inputText.split('\n');
      const numberoflines = lines.length;
      if (numberoflines == 1 && inputText == '') {
        addRow();
      }
      lines.forEach(line => {
        const [key, ...valueParts] = line.split(':');
        const value = valueParts.join(':').trim();
        const escapedValue = value.replace(/"/g, '&quot;');
        if (key) {
          const newRow = document.createElement('tr');
          newRow.innerHTML = `
              <td><input type="text" class="form-control" name="key[]" onchange="updateBulkInsert()" value="${key.trim()}" required/></td>
              <td><input type="text" class="form-control" name="value[]" onchange="updateBulkInsert()" value="${escapedValue}" required/></td>
              <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(event, this)">Remove</button></td>
            `;
          tableBody.appendChild(newRow);
        }
      });
    });
  });

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function wrapEncryptedString(str) {
    const safe = escapeHtml(String(str || '').trim());
    return safe.replace(/.{1,80}/g, function (chunk) {
      return chunk + '\n';
    }).trim().replace(/\n/g, '<br>');
  }

  function formatJsonPreview(data) {
    let value = data;
    if (typeof value === 'string') {
      const trimmed = value.trim();
      try {
        value = JSON.parse(trimmed);
      } catch (e) {
        return '<pre class="enc-json-preview">' + escapeHtml(trimmed) + '</pre>';
      }
    }
    if (value === null || typeof value === 'undefined') {
      return '<pre class="enc-json-preview">null</pre>';
    }
    const pretty = JSON.stringify(value, null, 2);
    const highlighted = escapeHtml(pretty)
      .replace(/("(?:\\.|[^"\\])*")\s*:/g, '<span class="enc-json-key">$1</span>:')
      .replace(/:\s*("(?:\\.|[^"\\])*")/g, ': <span class="enc-json-string">$1</span>')
      .replace(/:\s*(-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)/g, ': <span class="enc-json-number">$1</span>')
      .replace(/:\s*(true|false)/g, ': <span class="enc-json-bool">$1</span>')
      .replace(/:\s*(null)/g, ': <span class="enc-json-null">$1</span>');
    return '<pre class="enc-json-preview">' + highlighted + '</pre>';
  }

  function formatValueForInput(value) {
    if (value === null || value === undefined) {
      return '';
    }
    if (typeof value === 'object') {
      return JSON.stringify(value);
    }
    return String(value);
  }

  function tryParseJsonObject(input) {
    const trimmed = String(input || '').trim();
    if (!trimmed.startsWith('{')) {
      return null;
    }
    try {
      const parsed = JSON.parse(trimmed);
      if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
        return parsed;
      }
    } catch (e) {
      return null;
    }
    return null;
  }

  function populateKeyValueFromObject(data) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';
    const entries = Object.entries(data || {});
    if (entries.length === 0) {
      addRow();
      return false;
    }
    entries.forEach(function ([key, value], index) {
      const newRow = document.createElement('tr');
      const keyTd = document.createElement('td');
      const valueTd = document.createElement('td');
      const actionTd = document.createElement('td');

      const keyInput = document.createElement('input');
      keyInput.type = 'text';
      keyInput.className = 'form-control changetext';
      keyInput.name = 'key[]';
      keyInput.required = true;
      keyInput.value = key;
      keyInput.onchange = updateBulkInsert;

      const valueInput = document.createElement('input');
      valueInput.type = 'text';
      valueInput.className = 'form-control changetext';
      valueInput.name = 'value[]';
      valueInput.required = true;
      valueInput.value = formatValueForInput(value);
      valueInput.onchange = updateBulkInsert;

      keyTd.appendChild(keyInput);
      valueTd.appendChild(valueInput);

      if (index > 0 || entries.length > 1) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm';
        removeBtn.textContent = 'Remove';
        removeBtn.onclick = function (event) {
          removeRow(event, this);
        };
        actionTd.appendChild(removeBtn);
      } else {
        actionTd.className = 'fix-width';
      }

      newRow.appendChild(keyTd);
      newRow.appendChild(valueTd);
      newRow.appendChild(actionTd);
      tableBody.appendChild(newRow);
    });
    updateBulkInsert();
    return true;
  }

  function loadEncryptedForEditing() {
    const defaultKeyChecked = document.getElementById('defaultKey').checked;
    const manualKeyChecked = document.getElementById('manualKey').checked;
    const encKey = document.getElementById('enc_key').value.trim();
    const encIv = document.getElementById('enc_iv').value.trim();
    const encryptedInput = document.getElementById('paste_encrypted_input').value.trim();

    if (!encryptedInput) {
      swal('Missing Input', 'Please paste an encrypted string or JSON object first.', 'warning');
      return;
    }

    const jsonData = tryParseJsonObject(encryptedInput);
    if (jsonData) {
      populateKeyValueFromObject(jsonData);
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        size: 'mini',
        icon: 'fa fa-check-circle',
        msg: 'Loaded JSON for editing'
      });
      return;
    }

    if (manualKeyChecked && (!encKey || !encIv)) {
      swal('Missing Key/IV', 'Please provide Encryption Key and IV.', 'warning');
      return;
    }

    const formData = new FormData(document.getElementById('EncryptDecryptForm'));
    formData.set('encrypted_input', encryptedInput);
    formData.append('decryptData', true);
    formData.append('useDefaultKey', defaultKeyChecked);

    $.ajax({
      url: 'controller/encryptDecryptController.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        let result;
        try {
          result = JSON.parse(response);
        } catch (e) {
          swal('Error!', 'Invalid response while decrypting.', 'error');
          return;
        }
        if (!result.decrypted_data || typeof result.decrypted_data !== 'object') {
          swal('Decrypt Failed', 'Could not decrypt. Check the encrypted string and Key/IV.', 'error');
          return;
        }
        populateKeyValueFromObject(result.decrypted_data);
        Lobibox.notify('success', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          size: 'mini',
          icon: 'fa fa-check-circle',
          msg: 'Loaded for editing'
        });
      },
      error: function () {
        swal('Error!', 'Error decrypting the string.', 'error');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const pasteInput = document.getElementById('paste_encrypted_input');
    if (!pasteInput) return;
    pasteInput.addEventListener('paste', function () {
      setTimeout(function () {
        if (pasteInput.value.trim()) {
          loadEncryptedForEditing();
        }
      }, 0);
    });
  });

  function generateEncryptedString() {
    const defaultKeyChecked = document.getElementById('defaultKey').checked;
    const manualKeyChecked = document.getElementById('manualKey').checked;

    const encKey = document.getElementById('enc_key').value.trim();
    const encIv = document.getElementById('enc_iv').value.trim();

    if (manualKeyChecked && (!encKey || !encIv)) {
      swal('Missing Key/IV', 'Please provide both Encryption Key and IV.', 'warning');
      return;
    }

    const keyInputs = document.querySelectorAll('input[name="key[]"]');
    const valueInputs = document.querySelectorAll('input[name="value[]"]');
    for (let i = 0; i < keyInputs.length; i++) {
      const key = keyInputs[i].value.trim();
      const value = valueInputs[i].value.trim();
      if (!key) {
        swal('Missing Key', 'Key fields must be filled out.', 'warning');
        return;
      }
    }

    const formData = new FormData(document.getElementById('EncryptDecryptForm'));
    formData.append("encryptData", true);
    formData.append("useDefaultKey", defaultKeyChecked);

    $.ajax({
      url: 'controller/encryptDecryptController.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        $('#encryptform .enc-hidden-div').removeClass('d-none');
        $('#encryptedResult').html(wrapEncryptedString(result.encrypted_data));
        $('#decryptedResult').html(formatJsonPreview(result.decrypted_data));
      },
      error: function () {
        swal('Error!', 'Error generating encrypted string.', 'error');
      }
    });
  }

  function generateDecryptedString() {
    const defaultKeyChecked = document.getElementById('defaultKey').checked;
    const manualKeyChecked = document.getElementById('manualKey').checked;
    const encKey = document.getElementById('enc_key').value.trim();
    const encIv = document.getElementById('enc_iv').value.trim();
    const encryptedInput = document.querySelector('textarea[name="encrypted_input"]').value.trim();
    if (manualKeyChecked && (!encKey || !encIv)) {
      swal('Missing Key/IV', 'Please provide Encryption Key and IV.', 'warning');
      return;
    }
    if (!encryptedInput) {
      swal('Missing Input', 'Please provide the encrypted data.', 'warning');
      return;
    }
    const formData = new FormData(document.getElementById('EncryptDecryptForm'));
    $('.dec-hidden-div').removeClass('d-none');
    formData.append("decryptData", true);
    formData.append("useDefaultKey", defaultKeyChecked);
    $.ajax({
      url: 'controller/encryptDecryptController.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        const result = JSON.parse(response);
        $('#decryptedResultNew').html(formatJsonPreview(result.decrypted_data));
      },
      error: function() {
        swal('Error!', 'Error decrypting the string.', 'error');
      }
    });
  }

  function generateTextEncryptedString() {
    const defaultKeyChecked = document.getElementById('defaultKey').checked;
    const manualKeyChecked = document.getElementById('manualKey').checked;

    const encKey = document.getElementById('enc_key').value.trim();
    const encIv = document.getElementById('enc_iv').value.trim();

    if (manualKeyChecked && (!encKey || !encIv)) {
      swal('Missing Key/IV', 'Please provide both Encryption Key and IV.', 'warning');
      return;
    }

    const textarea = document.querySelector('#text-encryptform textarea[name="multipleInsert_input"]');
    const inputText = textarea.value.trim();
    if (!inputText) {
      swal('Missing Input', 'Please enter the text to encrypt.', 'warning');
      return;
    }

    const formData = new FormData(document.getElementById('EncryptDecryptForm'));
    formData.append("textEncryptData", true);
    formData.append("useDefaultKey", defaultKeyChecked);

    $.ajax({
      url: 'controller/encryptDecryptController.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        const result = JSON.parse(response);
        $('#text-encryptform .enc-hidden-div').removeClass('d-none');
        $('#encryptedTextResult').html(wrapEncryptedString(result.encrypted_data));
        if (result.decrypted_data) {
          $('#decryptedTextResult').html(formatJsonPreview(result.decrypted_data));
        }
      },
      error: function() {
        swal('Error!', 'Error generating encrypted string.', 'error');
      }
    });
  }

  function generateTextDecryptedString() {
    const defaultKeyChecked = document.getElementById('defaultKey').checked;
    const manualKeyChecked = document.getElementById('manualKey').checked;
    const encKey = document.getElementById('enc_key').value.trim();
    const encIv = document.getElementById('enc_iv').value.trim();
    const encryptedInput = document.querySelector('textarea[name="text_decrypt_input"]').value.trim();
    if (manualKeyChecked && (!encKey || !encIv)) {
      swal('Missing Key/IV', 'Please provide Encryption Key and IV.', 'warning');
      return;
    }
    if (!encryptedInput) {
      swal('Missing Input', 'Please provide the encrypted data.', 'warning');
      return;
    }
    const formData = new FormData(document.getElementById('EncryptDecryptForm'));
    $('.dec-hidden-div').removeClass('d-none');
    formData.append("textDecryptData", true);
    formData.append("useDefaultKey", defaultKeyChecked);
    $.ajax({
      url: 'controller/encryptDecryptController.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        const result = JSON.parse(response);
        $('#decryptedTextResultNew').html(formatJsonPreview(result.decrypted_data));
      },
      error: function() {
        swal('Error!', 'Error decrypting the string.', 'error');
      }
    });
  }

  function clickable(id) {
    var inputField = document.getElementById(id);
    var tempInput = document.createElement('textarea');
    tempInput.value = inputField.textContent || inputField.innerText;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    Lobibox.notify('success', {
      pauseDelayOnHover: true,
      continueDelayOnInactiveTab: false,
      position: 'top right',
      icon: 'fa fa-check-circle',
      msg: "Copied the text successfully"
    });
  };
</script>