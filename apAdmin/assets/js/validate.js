$().ready(function () {

    $.validator.addMethod("pwcheck", function (value) {
        return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
            && /[A-Z]/.test(value) // has a lowercase letter
            && /\d/.test(value) // has a digit
    });

    $.validator.addMethod("addressCheck", function (value) {
        /*alert(value);*/
        /*alert((/^[A-Za-z ]+/.test(value) || value==""  ))*/
        return /^[A-Za-z 0-9\d=!,\n\-@._*]*$/.test(value) // consists of only these
            && (/\w*[a-zA-Z ]\w*/.test(value) || value == '')// has a lowercase letter
        // && /\d/.test(value) // has a digit
    });

    $.validator.addMethod("checkmobile", function (value) {
        return /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[^.0]?)?[6789]\d{9}$/.test(value) // consists of only these
            && /^[0-9]+$/.test(value) // has a valid number
            && /\d/.test(value) // has a digit
    }, jQuery.validator.format("Please enter a Valid Mobile Number."));


    jQuery.validator.addMethod("acceptName", function (value, element, param) {
        return value.match(new RegExp("." + param + "$"));
    });


    $.validator.addMethod("alpha", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z- ]*$/);
    }, jQuery.validator.format("Please enter a Valid Name."));

    $.validator.addMethod("number", function (value, element) {
        return this.optional(element) || value == value.match(/^[0-9-()+ ]*$/);
    }, jQuery.validator.format("Please enter a Valid No."));

    $.validator.addMethod("alphaLng", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z_]*$/);
    }, jQuery.validator.format("Please enter a Valid Name."));

    $.validator.addMethod("noSpace", function (value, element) {
        return value == '' || value.trim().length != 0;
    }, "No space please and don't leave it empty");

    $.validator.addMethod("hhmmss", function (value, element) {
        return this.optional(element) || /^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/.test(value);
    }, "Please enter duration in hh:mm:ss format");


    $.validator.addMethod("checkspace", function (value, element) {
        var trimmedValue = value.replace(/&nbsp;/g, ' ').replace(/<p>|<\/p>|<br\s*\/?>/g, '').trim();
        return trimmedValue.length > 0;
    }, "Content cannot be empty or just spaces.");

    $.validator.addMethod("minTrimLength", function (value, element, param) {
        return this.optional(element) || $.trim(value).length >= param;
    }, jQuery.validator.format("Please enter at least {0} characters."));

    $.validator.addMethod("minPlainTextLength", function (value, element, param) {
        var plain = (value || '')
            .replace(/&nbsp;/gi, ' ')
            .replace(/<br\s*\/?>/gi, ' ')
            .replace(/<[^>]*>/g, '');
        plain = $('<textarea/>').html(plain).text();
        return this.optional(element) || $.trim(plain).length >= param;
    }, jQuery.validator.format("Please enter at least {0} characters."));

    // $.validator.addMethod("number", function(value, element) {
    //     return this.optional(element) || value == value.match(/^[0-9]*$/);
    // }, jQuery.validator.format("Please enter a Valid Number."));

    // $.validator.addMethod("decimal", function(value, element) {
    //     return this.optional(element) || value == value.match(/^[0-9.]*$/);
    // }, jQuery.validator.format("Please enter a Valid Number."));

    $.validator.addMethod('minStrict', function (value, el, param) {
        return value >= param;
    });

    jQuery.validator.addMethod("greaterThan",
        function (value, element, params) {

            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));
        }, 'Must be greater than {0}.');

    jQuery.validator.addMethod("image", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, jQuery.validator.format("Please add a valid image file."));


    $('input[type="file"]').change(function () {
        $(this).valid()
    });

    //IS_639 divya
    $.validator.addMethod('filesize', function (value, element, arg) {
        var size = 3000000;
        if (element.files.length) {
            if (element.files[0].size <= size) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }

    }, $.validator.format("file size must be less than or equal to 3MB."));

    //21FEB2020
    $.validator.addMethod('filesize1MB', function (value, element, arg) {
        var size = 1097152;
        if (element.files.length) {
            if (element.files[0].size < size) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, $.validator.format("file size must be less than  1MB."));

    $.validator.addMethod('MaxUploadFile', function (value, element, arg) {
        var FIlelength = 20;

        if (element.files.length <= FIlelength) {

            return true;
        } else {
            return false;
        }
    }, $.validator.format("You are allowed to upload only maximum 20 files at a time"));

    $.validator.addMethod('filesize20MBAll', function (value, element) {
        var size = 20 * 1024 * 1024;
        if (!element.files || !element.files.length) {
            return true;
        }
        for (var i = 0; i < element.files.length; i++) {
            if (element.files[i].size > size) {
                return false;
            }
        }
        return true;
    }, $.validator.format("Each file size must be less than or equal to 20MB."));

    $.validator.addMethod("qaPatchAttachmentExt", function (value, element) {
        if (!element.files || !element.files.length) {
            return true;
        }
        var allowed = /\.(jpe?g|png|webp|gif)$/i;
        for (var i = 0; i < element.files.length; i++) {
            if (!allowed.test(element.files[i].name)) {
                return false;
            }
        }
        return true;
    }, $.validator.format("Please upload image files only (jpg, jpeg, png, webp, gif)."));

    $.validator.addMethod("qaPatchAttachmentMax", function (value, element) {
        var maxImages = 5;
        var existing = $('#qaPatchExistingFiles input[name="keep_files[]"]').length || 0;
        var selected = (element.files && element.files.length) ? element.files.length : 0;
        var remaining = maxImages - existing;
        $(element).data('qaPatchRemaining', remaining < 0 ? 0 : remaining);
        return (existing + selected) <= maxImages;
    }, function (params, element) {
        var remaining = parseInt($(element).data('qaPatchRemaining'), 10);
        if (isNaN(remaining)) {
            remaining = 5;
        }
        if (remaining <= 0) {
            return "Maximum 5 images already attached. Remove an existing image to add a new one.";
        }
        return "Maximum 5 images allowed in total. You can add only " + remaining + " more.";
    });

    $.validator.addMethod("qaPatchVideoExt", function (value, element) {
        if (!element.files || !element.files.length) {
            return true;
        }
        if (element.files.length > 1) {
            return false;
        }
        return /\.(mp4|webm)$/i.test(element.files[0].name);
    }, $.validator.format("Please upload one video file only (mp4, webm)."));

    //21FEB2020


    //24feb2020
    $.validator.addMethod("extension", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, $.validator.format("Please select a file with a valid extension (png,jpeg,jpg,gif)."));

    $.validator.addMethod("extensionDoc", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif|pdf";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, $.validator.format("Please select a file with a valid extension (png,jpeg,jpg,gif,pdf)."));

    $.validator.addMethod('filesize4MB', function (value, element, arg) {
        var size = 4000000;
        if (element.files.length) {
            if (element.files[0].size <= size) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, $.validator.format("file size must be less than or equal to 4MB."));
    //24feb2020
    $.validator.addMethod('filesize2MB', function (value, element, arg) {
        var size = 2000000;
        if (element.files.length) {
            if (element.files[0].size <= size) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, $.validator.format("file size must be less than or equal to 2MB."));

    $.validator.addMethod("extensionDocument", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|txt|xls|csv|pdf|docx|odt|ods|xlsx";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, $.validator.format("Please select a file with a valid extension (png,jpeg,jpg,txt,xls,csv,pdf,docx,odt,ods,xlsx)."));

    $.validator.addMethod('compare', function (value, element, param) {
        return this.optional(element) || parseInt(value) > 0 || parseInt($(param).val()) > 0;
    }, $.validator.format('Invalid value'));

    jQuery.validator.addMethod("validSize", function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, jQuery.validator.format("Please add a Image smaller than 5 MB."));
    $.validator.addMethod("onlyLowercase", function (value, element) {
        return this.optional(element) || /^[a-z0-9]+$/.test(value);
    }, "Only lowercase letters and numbers are allowed.");


    $.validator.addMethod("notApadmin", function (value, element) {
        return this.optional(element) || !/apadmin/.test(value);
    }, "The value cannot contain 'apadmin'.");

    $("#chngProfileFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            old_password: {
                required: true,
                minlength: 1
            },
            password: {
                required: true,
                pwcheck: true,
                minlength: 5
            },
            password2: {
                required: true,
                pwcheck: true,
                minlength: 5,
                equalTo: "#password"
            },
        },
        messages: {
            password: {
                required: "Please provide a new password !",
                pwcheck: "Please enter atleasst 1 Uppercase letter & 1 number value",
                minlength: "Your password must be at least 5 characters long"
            },
            password2: {
                required: "Please provide a confirm password !",
                minlength: "Your password must be at least 5 characters long",
                pwcheck: "Please enter atleasst 1 Uppercase letter & 1 number value",
                equalTo: "Please enter the same password"
            }

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#personal-info").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });



    $("#faqQuestionValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            faq_question: {
                required: true,
                noSpace: true
            },
            faq_answer: {
                required: true,
                noSpace: true
            },
            platform_type: {
                required: true,
            }

        },
        messages: {
            faq_question: {
                required: "Please enter faq question",
            },

            faq_answer: {
                required: "Please enter faq aswer"
            },

            platform_type: {
                required: "Please enter Platform Type",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }

    });

    //IS_914 //2march2020 -new

    $("#addPackage").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            app_package_name: {
                required: true,
                noSpace: true
            }

        },
        messages: {
            app_package_name: {
                required: "Please Enter Name",
            }


        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }

    });



    $("#personal-info2").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#personal-info3").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            employment_type: {
                required: true,
            },
            business_categories: {
                required: true,
            },
            business_categories_sub: {
                required: true,
            },
            company_name: {
                required: true,
                noSpace: true,
            },
            designation: {
                required: true,
                noSpace: true,
                alpha: true,
            },
            company_address: {
                required: true,
                noSpace: true,
            },
            company_contact_number: {
                required: true,
                digits: true,
                maxlength: 13,
                minlength: 8,
            },
            employment_description: {
                required: true,
                noSpace: true,
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $(".common-form").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#signupForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            firstname: "required",
            lastname: "required",
            username: {
                required: true,
                minlength: 2
            },
            password: {
                required: true,
                minlength: 5
            },
            confirm_password: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            },
            contactnumber: {
                required: true,
                minlength: 8,
                maxlength: 13

            },
            topic: {
                required: "#newsletter:checked",
                minlength: 2
            },
            agree: "required"
        },
        messages: {
            firstname: "Please enter your firstname",
            lastname: "Please enter your lastname",
            username: {
                required: "Please enter a username",
                minlength: "Your username must consist of at least 2 characters"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            email: "Please enter a valid email address",
            contactnumber: "Please enter your 10 digit number",
            agree: "Please accept our policy",
            topic: "Please select at least 2 topics"
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#addUser").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            user_first_name_add: {
                required: true,
                alpha: true,
                noSpace: true,
            },
            user_last_name: {
                required: true,
                alpha: true,
                noSpace: true,
            },
            owner_first_name: {
                required: true,
                alpha: true,
                noSpace: true,
            },
            owner_last_name: {
                required: true,
                alpha: true,
                noSpace: true,
            },
            user_email: {
                minlength: 2
            },
            user_mobile: {
                required: true,
                minlength: 8,
                maxlength: 13

            },
            owner_mobile: {
                required: true,
                minlength: 8,
                maxlength: 13

            }

        },
        messages: {
            user_first_name_add: {
                required: "Please enter firstname",
                noSpace: "Space Not Allowed",
            },
            user_last_name: {
                required: "Please enter lastname",
                noSpace: "Space Not Allowed",
            },
            owner_first_name: {
                required: "Please enter firstname",
                noSpace: "Space Not Allowed",
            },
            owner_last_name: {
                required: "Please enter lastname",
                noSpace: "Space Not Allowed",
            },
            username: {
                required: "Please enter a username",
                minlength: "Your username must consist of at least 2 characters"
            },

            user_email: "Please enter a valid email address",
            user_mobile: "Please enter  10 digit mobile number",
            owner_mobile: "Please enter  10 digit mobile number"

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#buildingDetails").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            society_name_update: {
                required: true,
                noSpace: true,
            },
            society_address: {
                required: true,
                noSpace: true,
            },
            owner_mobile: {
                required: true,
                minlength: 8,
                maxlength: 13

            }

        },
        messages: {
            society_name_update: {
                required: "Please enter Company",
                noSpace: "No space please and don't leave it empty",
            },
            society_address: {
                required: "Please enter address",
                noSpace: "No space please and don't leave it empty",
            },

            builder_mobile: "Please enter 10 digit mobile number",

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#blockAdd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            block_name: {
                required: true,
                noSpace: true,
                //IS_159 28feb2020
                remote:
                {
                    url: 'ajaxRemote.php',
                    type: "post",
                    data:
                    {
                        block_name: function () {
                            return $('#blockAdd :input[name="block_name"]').val();
                        },
                        society_id: function () {
                            return $('#blockAdd :input[name="society_id"]').val();
                        }
                        //2march2020
                        ,
                        block_id: function () {
                            return $('#blockAdd :input[name="block_id"]').val();
                        }
                        //2march2020
                    }
                }
                //IS_159 28feb2020
            },
            block_sort: {
                required: true,
                noSpace: true,
                remote: {
                    url: 'ajaxRemote.php',
                    type: "post",
                    data: {
                        block_sort: function () {
                            return $('#blockAdd :input[name="block_sort"]').val();
                        },
                        society_id: function () {
                            return $('#blockAdd :input[name="society_id"]').val();
                        },
                        block_id: function () {
                            return $('#blockAdd :input[name="block_id"]').val();
                        }
                    }
                }
            },

            blockNameSingle: {
                required: true,
                noSpace: true,
                //IS_159 28feb2020
                remote:
                {
                    url: 'ajaxRemote.php',
                    type: "post",
                    data:
                    {
                        blockNameSingle: function () {
                            return $('#blockAdd :input[name="blockNameSingle"]').val();
                        },
                        society_id: function () {
                            return $('#blockAdd :input[name="society_id"]').val();
                        }
                    }
                }
                //IS_159 28feb2020
            },
            unit_name_single: {
                required: true,
                noSpace: true,
            }

        },
        messages: {
            block_name: {
                required: "Please enter block name",
                noSpace: "No space please and don't leave it empty",
                //IS_159 28feb2020
                remote: "Block Name is already exists, please use another Block name to avoid confusion"
                //IS_159  28feb2020
            },

            //7march2020
            block_sort: {
                required: "Please enter block order",
                noSpace: "No space please and don't leave it empty",
                remote: "Block order is already exists, please use another Block order to avoid confusion"

            },
            //7march2020

            blockNameSingle: {
                required: "Please enter block name",
                noSpace: "No space please and don't leave it empty",
                //IS_159 28feb2020
                remote: "Block Name is already exists, please use another Block name to avoid confusion"
                //IS_159 28feb2020
            },
            unit_name_single: {
                required: "Please enter unit name",
                noSpace: "No space please and don't leave it empty",
            },

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#editProfileFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            admin_name: {
                required: true,
                noSpace: true,
            },
            admin_email: {
                required: true,
                email: true,
                noSpace: true,
            },
            profile_image: {
                required: function (element) {
                    return $("#profile_image_old").val() == "";
                },
                filesize: true
            }

        },
        messages: {
            admin_name: {
                required: "Please enter admin name",
                noSpace: "No space please and don't leave it empty",
            },
            admin_email: {
                required: "Please enter email",
                email: "Please enter valid email",
                noSpace: "No space please and don't leave it empty",
            },
            profile_image: {
                required: "Please select image",
                noSpace: "No space please and don't leave it empty",
            },

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#documentTypeAdd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            document_type_name: {
                required: true,
                noSpace: true,
            },
            employee_type_name_edit: {
                required: true,
                noSpace: true,
            }


        },
        messages: {
            document_type_name: {
                required: "Please enter type name",
                noSpace: "No space please and don't leave it empty",
            },
            employee_type_name_edit: {
                required: "Please enter type name",
                noSpace: "No space please and don't leave it empty",
            },

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addDocument").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            ducument_name_add: {
                required: true,
                noSpace: true,
            },
            ducument_name_edit: {
                required: true,
                noSpace: true,
            }
            //IS_738 26feb2020
            , document_file: {
                extensionDocument: true,
                filesize: true
            }
            //IS_738 26feb2020

        },
        messages: {
            ducument_name_add: {
                required: "Please enter name",
                noSpace: "No space please and don't leave it empty",
            },
            ducument_name_edit: {
                required: "Please enter name",
                noSpace: "No space please and don't leave it empty",
            },

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#sosAdd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            event_name_edit: {
                required: true,
                noSpace: true,
            },
            event_name: {
                required: true,
                noSpace: true,
            },
            sos_image: {
                required: {
                    depends: function (element) {
                        return $("#img_req").val() == "0";
                    }
                },
                filesize: true
            },
            sos_duration: {
                required: true,
                min: 0,
                max: 120
            }


        },
        messages: {
            event_name_edit: {
                required: "Please enter sos name",
                noSpace: "No space please and don't leave it empty",
            },
            event_name: {
                required: "Please enter sos name",
                noSpace: "No space please and don't leave it empty",
            },
            sos_image: {
                required: "Please Select SOS Image"
            },
            sos_duration: {
                required: "Please enter SOS minutes",
                min: "Please enter minutes in Positive number",
                max: "Max SOS duration can be 2 hours, i.e. 120 minutes"
            }

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#insTeam").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            full_name: {
                required: true,
                noSpace: true,
            },
            mobile_number: {
                required: true,
                noSpace: true,
                minlength: 8,
                maxlength: 13,
            }

        },
        messages: {
            full_name: {
                required: "Please enter name",
                noSpace: "No space please and don't leave it empty",
            },
            mobile_number: {
                required: "Please enter mobile number",
                noSpace: "No space please and don't leave it empty",
            },


        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#editinsTeam").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            member_full_name: {
                required: true,
                noSpace: true,
            },
            member_mobile_number: {
                required: true,
                noSpace: true,
                minlength: 8,
                maxlength: 13,
            }

        },
        messages: {
            member_full_name: {
                required: "Please enter name",
                noSpace: "No space please and don't leave it empty",
            },
            member_mobile_number: {
                required: "Please enter mobile number",
                noSpace: "No space please and don't leave it empty",
            },


        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    ;
    $("#importValidation").validate({
        rules: {
            file: {
                required: true,
                extension: "csv",
            }
        },
        messages: {
            file: {
                extension: "Please upload CSV file only"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#notificationValidation").validate({
        rules: {
            title: {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
            },
            description: {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#pendingValidation").validate({
        rules: {
            user_first_name: {
                required: true,
                noSpace: true,
                alpha: true,
            },
            user_last_name: {
                required: true,
                noSpace: true,
                alpha: true,
            },
            user_mobile: {
                required: true,
                noSpace: true,
                digits: true,
                maxlength: 13,
                minlength: 8,
            },
            user_email: {
                required: true,
                noSpace: true,
                email: true,
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#complainCategoryValidation").validate({
        rules: {
            category_name: {
                required: true,
                noSpace: true,
                maxlength: 25,
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    //3 March
    $("#complaintValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            user_id: {
                required: true,
            },
            complaint_category: {
                required: true,
            },
            compalain_title: {
                required: true,
                noSpace: true,
                maxlength: 100,
            },
            complain_photo: {
                image: true,
            },
            complain_description: {
                noSpace: true,
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    //12march2020




    //13march2020
    $("#addMainVisitorFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            main_type_name:
            {
                required: true,
                noSpace: true
            },
            visitor_type: {
                required: true,
                noSpace: true
            },
            main_type_image:
            {
                required: true,
                filesize4MB: true
            },
            visitor_main_full_img:
            {
                required: true,
                filesize4MB: true
            },
        },
        messages: {
            main_type_name: {
                required: "Please enter visitor type name",
            },
            visitor_type: {
                required: "Please enter visitor type",
            },
            main_type_image: {
                required: "Please select main type image",
            },
            visitor_main_full_img: {
                required: "Please select full image",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#visitorSubTypeFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            visitor_main_type_id:
            {
                required: true,
                noSpace: true
            },
            visitor_sub_type_name: {
                required: true,
                noSpace: true
            },
            visitor_sub_image:
            {
                required: true,
                filesize4MB: true
            }
        },
        messages: {
            visitor_main_type_id: {
                required: "Please select main type",
            },
            visitor_sub_type_name: {
                required: "Please enter sub type name",
            },
            visitor_sub_image: {
                required: "Please select image",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    //7 Jul 2020 - Mahima
    $("#sliderImageAddValidation").validate({
        rules: {
            slider_image: {
                required: function (element) {
                    return $("#profile_image_old").val() == "";
                },
                validSize: 5000000,
                image: true,
                required: true
            },
            page_mobile: {
                maxlength: 13,
                minlength: 8
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#sliderImageAddValidationEdit").validate({
        rules: {
            slider_image: {
                required: function (element) {
                    return $("#profile_image_old").val() == "";
                },
                validSize: 5000000,
                image: true,
            },
            page_mobile: {
                maxlength: 13,
                minlength: 8
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });



    $("#addUserValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            admin_name: {
                required: true,
                alpha: true,
                maxlength: 50,
                noSpace: true,
            },
            admin_email: {
                required: true,
                email: true,
                maxlength: 100
            },
            admin_mobile: {
                required: true,
                maxlength: 13,
                minlength: 8,
                digits: true,
            },
            role_id: {
                required: true,
            },
            admin_profile: {
                image: true,
                filesize2MB: true,
            },
            user_id: {
                remote: {
                    url: "./controller/userController.php",
                    type: "POST",
                    data: {
                        checkMycoUserBind: "checkMycoUserBind",
                        user_id: function () {
                            return $("#myco_user_bind").val();
                        },
                        admin_id: function () {
                            return $('input[name="admin_id"]').val() || '';
                        },
                        csrf: function () {
                            return typeof csrf !== 'undefined' ? csrf : $('input[name="csrf"]').val();
                        }
                    },
                    dataFilter: function (response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.valid) {
                                return "true";
                            }
                            if (result.message) {
                                return '"' + result.message.replace(/"/g, '\\"') + '"';
                            }
                            return "false";
                        } catch (e) {
                            return "true";
                        }
                    }
                }
            },
        },
        messages: {
            user_id: {
                remote: "This MyCo user is already bound to another admin."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#addSocietyRequestValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            society_name: {
                required: true,
                maxlength: 50,
                noSpace: true,
            },
            society_name_edit: {
                required: true,
                maxlength: 50,
                noSpace: true,
            },
            country_id: {
                required: true,
            },
            sales_person_name: {
                required: true,
            },
            employee_registration_limit: {
                required: true,
            },
            employee_tracking_limit: {
                required: true,
            },
            per_employee_price: {
                required: true,
            },
            state_id: {
                required: true,
            },
            city_id: {
                required: true,
            },
            society_type: {
                required: true,
            },
            society_address: {
                required: true,
                maxlength: 250,
            },
            secretary_name: {
                required: true,
                maxlength: 50,
            },
            secretary_email: {
                required: true,
                email: true,
                maxlength: 100
            },
            secretary_mobile: {
                required: true,
                maxlength: 13,
                minlength: 8,
                digits: true,
            },
            sub_domain: {
                required: true,
                url: true,
            },
            end_url_name: {
                required: true,
                onlyLowercase: true,
                notApadmin: true
            },
            society_pincode: {
                required: false,
                digits: true,
                maxlength: 6,
            },
            package_id: {
                required: true,
            },
            trial_days: {
                required: true,
                digits: true,
                max: 100,
                min: 1,
            },
            amountReceivedType: {
                required: true
            },
            amountReceived: {
                required: true,
                minlength: 1,
                maxlength: 10,
            },
            builder_name: {
                maxlength: 50,
            },
            builder_mobile: {
                digits: true,
                maxlength: 13,
                minlength: 8,
            },
            builder_address: {
                maxlength: 250
            },
            society_logo: {
                filesize: true,
                image: true
            },
            support_name: {
                required: true,
                maxlength: 100,
            },
            support_mobile_no: {
                required: true,
                maxlength: 16,
            },
            society_code: {
                required: function (element) {
                    return $('input[name="search_society_code"]:checked').val() == "1";
                },
                maxlength: 20,
                minlength: 3,
                noSpace: true,
            },
            society_code_edit: {
                required: function (element) {
                    return $('input[name="search_society_code_edit"]:checked').val() == "1";
                },
                maxlength: 20,
                minlength: 3,
                noSpace: true,
            },
            region: {
                required: true,
            },
        },

        submitHandler: function (form) {
            let isValid = true;
            $('.contact-form').each(function () {
                const nameInput = $(this).find('.contact-name');
                const numberInput = $(this).find('.contact-number');
                const designationInput = $(this).find('.contact-designation');

                if (!nameInput.valid()) isValid = false;
                if (!numberInput.valid()) isValid = false;
                if (!designationInput.valid()) isValid = false;
            });
            if (!isValid) {
                return false;
            }
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    if (window.location.href.toLowerCase().includes("addcompany") && typeof reindexContacts == "function") {
        reindexContacts();
    }

    $("#rejectSocForm").validate({
        rules: {
            admin_name: {
                required: true,
                maxlength: 150,
                noSpace: true,
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#addTimelineFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            timeline_text: {
                required: true,
                noSpace: true,
            },
            image: {
                required: true,
                filesize4MB: true
            }
        }, messages: {
            timeline_text: {
                required: "Please enter Title"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#editTimelineFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            timeline_text: {
                required: true,
                noSpace: true,
            },
            image: {
                required: false,
                filesize4MB: true
            }
        }, messages: {
            timeline_text: {
                required: "Please enter Title"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addFestival").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            festival_name: {
                required: true,
                noSpace: true,
            },
            festival_image: {
                required: false,
                filesize4MB: true
            }
        }, messages: {
            festival_name: {
                required: "Please enter festival name"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    //29sept2020
    //30sept2020
    $("#publishTimelineFrm").validate({
        errorPlacement: function (error, element) {
            if (element.attr("type") == "checkbox") {

                $("#chkError").addClass('text-danger');
                $("#chkError").html("<center><b>Please add atleast one Company .</b></center>");
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            sosa_city_id: { required: true },
            "society_id[]": "required"
        }, messages: {
            sosa_city_id: { required: "Please select city." },
            "society_id[]": "Please select atleast one society"
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    //29sept2020
    //MSTCHANGEOCT2020

    $("#langFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            language_name: { required: true, noSpace: true },
            language_name_1: { required: true, noSpace: true },
            continue_btn_name: { required: true, noSpace: true }
        }, messages: {
            language_name: { required: "Please enter language name." },
            language_name_1: { required: "Please enter language name 1." },
            continue_btn_name: { required: "Please enter continue button name." },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#langKeyFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            key_name: {
                required: true,
                noSpace: true,
                alphaLng: true,
                remote:
                {
                    url: 'UniqueLangKeyName.php',
                    type: "post",
                    data:
                    {
                        key_name: function () {
                            return $('#langKeyFrm :input[name="key_name"]').val();
                        }
                    }
                }
            },
            language_key_name: {
                required: true,
                noSpace: true,
            }
        }, messages: {
            key_name: {
                required: "Please enter key.",
                remote: "Key Name is already exists, please use another Key name to avoid confusion"

            },
            language_key_name: {
                required: "Please valid enter key.",
            },
            submitHandler: function (form) {
                $(':input[type="submit"]').prop('disabled', true);
                $(".ajax-loader").show();
                form.submit();
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#EditlangKeyFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            key_name: {
                required: true,
                noSpace: true,
                alphaLng: true,
                remote:
                {
                    url: 'UniqueLangKeyName.php',
                    type: "post",
                    data:
                    {
                        language_key_id: function () {
                            return $('#EditlangKeyFrm :input[name="language_key_id"]').val();
                        },
                        key_name: function () {
                            return $('#EditlangKeyFrm :input[name="key_name"]').val();
                        }
                    }
                }
            }
        }, messages: {
            key_name: {
                required: "Please enter key.",
                remote: "Key Name is already exists, please use another Key name to avoid confusion"

            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#langKeyFrmGd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            key_name: {
                required: true,
                noSpace: true,
                alphaLng: true,
                remote:
                {
                    url: 'UniqueLangKeyNameGd.php',
                    type: "post",
                    data:
                    {
                        key_name: function () {
                            return $('#langKeyFrmGd :input[name="key_name"]').val();
                        }
                    }
                }
            }
        }, messages: {
            key_name: {
                required: "Please enter key.",
                remote: "Key Name is already exists, please use another Key name to avoid confusion"

            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#EditlangKeyFrmGd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            key_name: {
                required: true,
                noSpace: true,
                alphaLng: true,
                remote:
                {
                    url: 'UniqueLangKeyNameGd.php',
                    type: "post",
                    data:
                    {
                        language_key_id: function () {
                            return $('#EditlangKeyFrmGd :input[name="language_key_id"]').val();
                        },
                        key_name: function () {
                            return $('#EditlangKeyFrmGd :input[name="key_name"]').val();
                        }
                    }
                }
            }
        }, messages: {
            key_name: {
                required: "Please enter key.",
                remote: "Key Name is already exists, please use another Key name to avoid confusion"

            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#editLanguageValueKeyFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            'value_name[]': {
                required: true,
                noSpace: true
            }

        }, messages: {
            'value_name[]': {
                required: "Please enter velue.",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#replyFeedbackFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            reply: {
                required: true,
                noSpace: true
            }

        }, messages: {
            reply: {
                required: "Please enter Reply.",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#remarkFeedbackFrm").validate({
        rules: {
            closing_remarks: {
                required: true,
                noSpace: true
            }

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#reopenRemarkFeedbackFrm").validate({
        rules: {
            reopen_remarks: {
                required: true,
                noSpace: true
            }

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#planUpdateValidation").validate({
        rules: {
            update_plan: {
                required: true,
                noSpace: true
            }

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $.validator.addMethod("firstLetterCapital", function (value, element) {
        return this.optional(element) || /^[A-Z]/.test(value);
    }, "First letter must be uppercase.");

    $("#planChangeModal").validate({
         errorPlacement: function (error, element) {
            if (element.attr('name') === 'update_limits') {
                error.addClass('d-block mt-1 mb-0');
                error.appendTo(element.closest('.col-sm-8'));
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            transaction_amount_remark: {
                required: true,
                minlength: 3,
                maxlength: 150,
                noSpace: true
            },
            received_by: {
                required: true,
                noSpace: true
            },
            closure_city: {
                required: true,
                minlength: 3,
                firstLetterCapital: true
            },
            plan_type: {
                required: true,
            },
            package_id: {
                required: true,
            },
            plan_expire_date: {
                required: true,
            },
            amountReceived: {
                required: true,
            },
            payment_mode: {
                required: true,
            },
            payment_attachment: {
                required: function () {
                    return $('#plan_type').val() == '1';
                }
            },
            update_limits: {
                required: true
            },
            employee_tracking_limit: {
                required: true,
                digits: true
            },
            employee_registration_limit: {
                required: true,
                digits: true
            },
            crm_limit: {
                required: true,
                digits: true
            }
        },
        messages: {
            transaction_amount_remark: {
                minlength: "Remark must be at least 3 characters long.",
                maxlength: "Remark must not exceed 150 characters."
            },
            closure_city: {
                required: "Closure City is required.",
                minlength: "Closure City must be at least 3 characters long.",
                firstLetterCapital: "First letter must be uppercase."
            },
            received_by: {
                required: "Received by is required."
            },
            plan_type: {
                required: "Please Select Plan Type"
            },
            package_id: {
                required: "Please Select Package Id"
            },
            plan_expire_date: {
                required: "Please Select Plan Expire Date"
            },
            amountReceived: {
                required: "Please Enter Amount"
            },
            payment_mode: {
                required: "Please Select Payment Mode"
            },
            payment_attachment: {
                required: "Payment attachment is required for Renewal."
            },
            update_limits: {
                required: "Please select whether to update the limit."
            },
            employee_tracking_limit: {
                required: "Please enter the employee tracking limit.",
                digits: "Please enter a valid tracking limit."
            },
            employee_registration_limit: {
                required: "Please enter the employee registration limit.",
                digits: "Please enter a valid registration limit."
            },
            crm_limit: {
                required: "Please enter the CRM limit.",
                digits: "Please enter a valid CRM limit."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addFeedbackForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            companyId: {
                required: true,
            },
            name: {
                required: true,
                noSpace: true,
            },
            mobile: {
                required: true,
                noSpace: true,
            },
            email: {
                required: true,
                email: true,
                maxlength: 100,
            },
            subject: {
                required: true,
                noSpace: true,
            },
            feedback_msg: {
                required: true,
                noSpace: true,
            },
        }, submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    // Initialize the validation
    $("#seasonalGreetFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            title: {
                required: true,
                noSpace: true
            },
            start_date: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetFrm :input[name="is_expiry"]').val() == "Yes";
                    }
                },
                noSpace: true
            },
            end_date: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetFrm :input[name="is_expiry"]').val() == "Yes";
                    }
                },
                noSpace: true
            },
            'background_image[0]': {  // Validation rule for the image field
                required: function () {
                    return $('[name^="background_image[0]"]').length > 0;
                }
            }
        },
        messages: {
            title: {
                required: "Please provide title",
            },
            start_date: {
                required: "Please select start date"
            },
            end_date: {
                required: "Please select end date"
            },
            'background_image[0]': {
                required: "Please upload an image",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#seasonalGreetImageFrm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            cover_image: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetImageFrm :input[name="cover_image_old"]').val() == "";
                    }
                },
                filesize: true,
                image: true
            },
            background_image: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetImageFrm :input[name="background_image_old"]').val() == "";
                    }
                },
                filesize: true,
                image: true
            },
            title_on_image: {
                required: false,
                noSpace: false
            },
            description_on_image: {
                required: false,
                noSpace: false
            },
            page_alignment: {
                required: true,
            },
            title_font_name: {
                required: true,
            },
            description_font_name: {
                required: true,
            },
            to_name_font_name: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetImageFrm :input[name="show_to_name"]').val() == "Yes";
                    }
                },
                noSpace: true
            },
            from_name_font_name: {
                required: {
                    depends: function (element) {
                        return $('#seasonalGreetImageFrm :input[name="show_from_name"]').val() == "Yes";
                    }
                },
                noSpace: true
            }
        },
        messages: {
            cover_image: {
                required: "Please select cover image"
            },
            background_image: {
                required: "Please select background image"
            },
            title_on_image: {
                required: "Please provide title on image",
            },
            description_on_image: {
                required: "Please provide description on image",
            },
            page_alignment: {
                required: "Please select Page alignment",
            },
            title_font_name: {
                required: "Please select title font",
            },
            description_font_name: {
                required: "Please select description font",
            },
            to_name_font_name: {
                required: "Please select to name font",
            },
            from_name_font_name: {
                required: "Please select from name font",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#notificationWebValidation").validate({
        rules: {
            title: {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
            },
            description: {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#UserIOS").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            version_code: {
                required: true,
                noSpace: true,
                decimal: true,
            },
            version_name_view: {
                required: true,
                noSpace: true,
                decimal: true,
            },
            language_version: {
                required: true,
                noSpace: true,
                decimal: true,
            },
        },
        messages: {
            version_code: {
                required: "Please enter version code",
            },
            version_name_view: {
                required: "Please enter version view",
            },
            language_version: {
                required: "Please enter language version",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#CityAdd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 85,
            },
        },
        messages: {
            name: {
                required: "Please enter City name",
                maxlength: "Please enter maximum 85 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#UpdateCity").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 85,
            },
        },
        messages: {
            name: {
                required: "Please enter City name",
                maxlength: "Please enter maximum 85 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#StateAdd").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 50,
            },
        },
        messages: {
            name: {
                required: "Please enter State name",
                maxlength: "Please enter maximum 50 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#StateUpdate").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                maxlength: 50,
                alpha: true,
            },
        },
        messages: {
            name: {
                required: "Please enter State name",
                maxlength: "Please enter maximum 50 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#AddCountrys").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 56,
            },
            iso3: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 3,
            },
            iso2: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 2,
            },
            phonecode: {
                required: true,
                noSpace: true,
                number: true,
                maxlength: 8,
            },
            capital: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 50,
            },
            currency: {
                required: true,
                noSpace: true,
                maxlength: 3,
            },
        },
        messages: {
            name: {
                required: "Please enter name",
                maxlength: "Please enter maximum 56 characters",
            },
            iso3: {
                required: "Please enter iso3",
                maxlength: "Please enter maximum 3 characters",
            },
            iso2: {
                required: "Please enter iso2",
                maxlength: "Please enter maximum 2 characters",
            },
            phonecode: {
                required: "Please enter phonecode",
                maxlength: "Please enter maximum 8 characters",
            },
            capital: {
                required: "Please enter capital",
                maxlength: "Please enter maximum 50 characters",
            },
            currency: {
                required: "Please enter currency",
                maxlength: "Please enter maximum 3 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#CountryUpdate").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            name: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 56,
            },
            iso3: {
                required: true,
                noSpace: true,
                maxlength: 3,
            },
            iso2: {
                required: true,
                noSpace: true,
                maxlength: 2,
            },
            phonecode: {
                required: true,
                noSpace: true,
                number: true,
                maxlength: 8,
            },
            capital: {
                required: true,
                noSpace: true,
                maxlength: 50,
            },
            currency: {
                required: true,
                noSpace: true,
                maxlength: 3,
            },
        },
        messages: {
            name: {
                required: "Please enter name",
                maxlength: "Please enter maximum 56 characters",
            },
            iso3: {
                required: "Please enter iso3",
                maxlength: "Please enter maximum 3 characters",
            },
            iso2: {
                required: "Please enter iso2",
                maxlength: "Please enter maximum 2 characters",
            },
            phonecode: {
                required: "Please enter phonecode",
                maxlength: "Please enter maximum 8 characters",
            },
            capital: {
                required: "Please enter capital",
                maxlength: "Please enter maximum 50 characters",
            },
            currency: {
                required: "Please enter currency",
                maxlength: "Please enter maximum 3 characters",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    // DY 10-10-23 Start

    $("#RejectByDeveloperFeedbackFrm").validate({
        rules: {
            resion: {
                required: true,
                noSpace: true
            }
        },
        messages: {
            resion: {
                required: "Please enter reason",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    // DY 10-10-23 End

    $("#changeStatusUpdate").validate({
        rules: {
            status: {
                required: true,
            }
        },
        messages: {
            status: {
                required: "Please Select Status",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#addNewDeveloper").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            developer_name: {
                required: true,
                alpha: true,
                maxlength: 50,
                noSpace: true,
            },
            developer_technology: {
                required: true,
            }
        },
        messages: {
            developer_name: {
                required: "Please Enter developer full name.",
                maxlength: "Name must be under 50 characters.",
            },
            developer_technology: {
                required: "Please select the empployee technology",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#editDeveloper").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            developer_name: {
                required: true,
                alpha: true,
                maxlength: 50,
                noSpace: true,
            },
            developer_technology: {
                required: true,
            }
        },
        messages: {
            developer_name: {
                required: "Please Enter developer full name.",
                maxlength: "Name must be under 50 characters.",
            },
            developer_technology: {
                required: "Please select the empployee technology",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#devBulkImport").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.form-check-inline').length) {
                error.insertAfter(element.parent()); // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span')); // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element); // default
            }
        },
        rules: {
            file: "required",
        },
        messages: {
            file: "Please select your file.",
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#businessEntity, #editbusinessEntity").each(function () {
        $(this).validate({
            rules: {
                name1: {
                    noSpace: true,
                    required: true,
                },
                name: {
                    noSpace: true,
                    required: true,
                }
            },
            messages: {
                name1: {
                    required: "Please enter Business Name",
                },
                name: {
                    required: "Please enter Business Name",
                }
            },
            submitHandler: function (form) {
                $(':input[type="submit"]').prop('disabled', true);
                $(".ajax-loader").show();
                form.submit();
            }
        });
    });

    $("#developerNotes").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            company_selection: {
                required: true,
            },
            integration_type: {
                required: true,
            },
            integration_name: {
                required: true,
                noSpace: true
            },
            integration_description: {
                required: true,
                noSpace: true
            },

        },
        messages: {
            company_selection: {
                required: "Please select company",
            },
            integration_type: {
                required: "Please select integration type",
            },
            integration_name: {
                required: "Please enter integration name",
            },
            integration_description: {
                required: "Please enter integration descriptiona",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addmycoWhiteLabel").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            master_company_id: {
                required: true,
                noSpace: true,
                number: true
            },
            company_name: {
                required: true,
                noSpace: true,
                maxlength: 50
            },
            country_id: {
                required: true,
            },
            state_id: {
                required: true
            },
            city_id: {
                required: true,
            },
            society_address: {
                required: true,
                noSpace: true,
                maxlength: 100,
            },
            sub_domain: {
                required: true,
                noSpace: true,
                maxlength: 120
            },

        },
        messages: {
            master_company_id: {
                required: "Please enter company id",
            },
            company_name: {
                required: "Please enter company name",
                maxlength: "Company name must not exceed 100 characters."
            },
            country_id: {
                required: "Please select country",
            },
            state_id: {
                required: "Please select state",
            },
            city_id: {
                required: "Please select city",
            },
            society_address: {
                required: "Please enter society address",
                maxlength: "Company address should not exceed 100 characters."
            },
            sub_domain: {
                required: "Please enter company base url",
                maxlength: "Please keep the company base URL to a maximum of 120 characters."
            }
        }, submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#settingsForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            society_name: {
                required: true,
                noSpace: true,
                maxlength: 50,
            },
            company_full_name: {
                noSpace: true,
                maxlength: 250,
            },

            secretary_name: {
                required: true,
                noSpace: true,
                maxlength: 100,
            },
            society_address: {
                required: true,
                noSpace: true,
                maxlength: 300,
            },
            secretary_email: {
                required: true,
                email: true,
                maxlength: 100,
            },
            secretary_mobile: {
                required: true,
                minlength: 8,
                maxlength: 15,

            },
            society_pincode: {
                required: true,
                digits: true,
                maxlength: 6,
            },
            google_login: {
                required: true,
                noSpace: true,

            },
            industry_type: {
                required: true,
                noSpace: true,

            },
            gst_number: {
                noSpace: true,
                maxlength: 50,
            },
            society_code: {
                required: function (element) {
                    return $('#search_society_code').val() == "1";
                },
                maxlength: 20,
                minlength: 3,
                noSpace: true,
            },

        },
        messages: {
            society_name: {
                required: "Please enter company name",
            },
            company_full_name: {
                required: "Please enter company full name",
            },
            secretary_name: {
                required: "Please enter contact person name",
            },
            society_address: {
                required: "Please enter address",
            },
            secretary_email: {
                required: "Please enter valide email",
            },
            secretary_mobile: {
                required: "Please enter mobile number",
                minlength: "Please enter atleast 8 characters",
                maxlength: "Secretary mobile number must not exceed 15 characters",
            },
            society_pincode: {
                required: "Please enter pincode",
            },
            google_login: {
                required: "Please  select google login",
            },
            industry_type: {
                required: "Please select industry type",
            },
            gst_number: {
                required: "Please enter gst number",
            },
            society_code: {
                required: "Please enter company code",
                minlength: "Please enter atleast 3 characters",
                maxlength: "Company code must not exceed 15 characters",
            }

        }, submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addKycValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            kyc_api_name: {
                required: true,
                noSpace: true,
                maxlength: 250,
            },
            kyc_api_price: {
                required: true,
                maxlength: 13,
                decimal: true,

            },
            kyc_api_url: {
                required: true,
                noSpace: true,
                maxlength: 250,
            },
            api_method: {
                required: true,
                noSpace: true,
            },

        },
        messages: {
            kyc_api_name: {
                required: "Please enter kyc api name",
            },
            kyc_api_price: {
                required: "Please enter kyc api price",
            },
            kyc_api_url: {
                required: "Please enter kyc api url",
            },
            api_method: {
                required: "Please select kyc api method",
            },

        }, submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    // Mukesh End 12-6-24 - api key add validation

    // Mukesh Start 17-6-24 - FidyPay Edit  validation
    $("#editFidyPayValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            base_url: {
                required: true,
                noSpace: true,
                maxlength: 250,
            },
            client_id: {
                required: true,
                noSpace: true,
                maxlength: 250,

            },
            client_secret: {
                required: true,
                noSpace: true,
                maxlength: 250,
            },
            authorization: {
                required: true,
                noSpace: true,
                maxlength: 250,
            },
            bank_name: {
                required: true,
                noSpace: true,
                maxlength: 56,
            },
            account_number: {
                required: true,
                noSpace: true,
                number: true,
                maxlength: 20,
            },
            ifsc_code: {
                required: true,
                noSpace: true,
                maxlength: 28,
            },
        },
        messages: {
            kyc_api_name: {
                required: "Please enter Base Url",
            },
            client_id: {
                required: "Please enter Client ID",
            },
            client_secret: {
                required: "Please enter Client Secret",
            },
            authorization: {
                required: "Please enter Authorization",
            },
            bank_name: {
                required: "Please enter Bank Name",
            },
            account_number: {
                required: "Please enter Account Number",
            },
            ifsc_code: {
                required: "Please enter Ifsc Code",
            },

        }, submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addInstituteForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            institutePassword: {
                required: true,
                minlength: 5,
                noSpace: true
            },
            academicYearTitle: {
                required: true,
                noSpace: true
            },
            academicYearCode: {
                required: true,
                noSpace: true
            },
            academicYearStartDate: {
                required: true,
            },
            academicYearEndDate: {
                required: true,
            },
        },
        messages: {
            institutePassword: {
                required: "Please Enter Institute Password",
                minlength: "Please enter atleast 5 characters",
            },
            academicYearTitle: {
                required: "Please Enter Academic Year Title",
            },
            academicYearCode: {
                required: "Please Enter Academic Year Code",
            },
            academicYearStartDate: {
                required: "Please Select Start Date",
            },
            academicYearEndDate: {
                required: "Please Select End Date",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addKycApiCompanyPrice").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            society_id: {
                required: true,
            },
            kyc_api_type: {
                required: true,
                remote: {
                    url: "ajaxApiAssignCompany.php",
                    type: "post",
                    data: {
                        society_id: function () {
                            return $("#society_id_kycApi").val();
                        },
                        kyc_api_type: function () {
                            return $("#kyc_api_type_ccp").val();
                        },
                        action: function () {
                            return "checkApiAssignCompany";
                        },
                    },
                },
            },
            custom_price: {
                decimal: true,
            },
        },
        messages: {
            society_id: {
                required: "Please select company name",
            },
            kyc_api_type: {
                required: "Please select api name",
                remote: "Api allready asign",
            },
            custom_price: {
                decimal: "Please enter number or decimal value",
            },

        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $.validator.addMethod("ipAddress", function (value, element) {
        const ipRegex = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        return this.optional(element) || ipRegex.test(value);
    }, "Please enter a valid IP address");

    $("#serverForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            server_name: {
                required: true,
                noSpace: true,
                maxlength: 80,

            },
            server_ip: {
                required: true,
                noSpace: true,
                maxlength: 45,
                ipAddress: true,
            },
            server_remark: {
                maxlength: 250,
            }

        },
        messages: {
            server_name: {
                required: "Please Enter Server Name",
                maxlength: "Server Name must not exceed 80 characters",
            },
            server_ip: {
                required: "Please Enter Server Ip",
                maxlength: "Server IP must not exceed 45 characters",
            },
            server_remark: {
                maxlength: "Server Remark Must Not Exceed 250 characters",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#domainForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            domain_name: {
                required: true,
                noSpace: true,
                maxlength: 80,

            },
            server_id: {
                required: true,
            },
            domain_remark: {
                maxlength: 250,
            },
            addedInCloudflare: {
                required: true,
            },
            addedInFirebase: {
                required: true,
            },
            remote_ip: {
                required: true,
                noSpace: true,
            }
        },
        messages: {
            domain_name: {
                required: "Please Enter Domain Name",
                maxlength: "Domain Name must not exceed 80 characters",
            },
            server_id: {
                required: "Please Select Server Name",
            },
            domain_remark: {
                maxlength: "Domain Remark Must Not Exceed 250 characters",
            },
            addedInCloudflare: {
                required: "Please confirm that the subdomain is added in Cloudflare",
            },
            addedInFirebase: {
                required: "Please confirm that the subdomain is added in Firebase",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#whatsNewForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span')); // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: $.extend({
            title: {
                required: true,
                minlength: 5,
                maxlength: 50,
                noSpace: true,
            },
            platform: "required",
            version: {
                required: function () {
                    return $(".versionbox").is(":visible") ? true : false;
                },
                noSpace: true,
                remote: {
                    type: "POST",
                    url: './controller/whatsNewController.php',
                    data: {
                        platform: function () {
                            return $('#platform').val();
                        },
                        version: function () {
                            return $("#version").val();
                        },
                        patch_id: function () {
                            return $("#patch_id").val();
                        },
                        checkVersionValid: "checkVersionValid",
                        csrf: function () {
                            return $("input[name='csrf']").val();
                        },
                    },
                    dataFilter: function (response) {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.valid) {
                            return true;
                        } else {
                            return '"Please enter a version greater than old version ' + jsonResponse.max_version + '"';
                        }
                    }
                }
            }
        }, window.dynamicRules),
        messages: $.extend({
            title: {
                required: "Please enter title",
                minlength: "Please enter at least 5 characters.",
                maxlength: "Maximum 50 characters.",
            },
            platform: "Please select platform",
            version: {
                required: "Please enter version",
                remote: "Please enter version greater than old version",
            },
        }, window.dynamicMessages),
        ignore: ".note-editor *",
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#qaPatchDetailsForm").validate({
        errorPlacement: function (error, element) {
            if (element.next('.note-editor').length) {
                error.insertAfter(element.next('.note-editor'));
            } else if (element.attr('id') === 'qaPatchAttachments' && element.nextAll('.form-text').first().length) {
                error.insertAfter(element.nextAll('.form-text').first());
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        ignore: ".note-editor *",
        rules: {
            qa_patch_title: {
                required: true,
                minTrimLength: 5,
                noSpace: true
            },
            qa_patch_description: {
                required: true,
                checkspace: true,
                minPlainTextLength: 5
            },
            show_in_patch: {
                required: true
            },
            'qa_patch_platforms[]': {
                required: true
            },
            'patch_attachments[]': {
                qaPatchAttachmentMax: true,
                qaPatchAttachmentExt: true,
                filesize20MBAll: true
            },
            patch_video: {
                qaPatchVideoExt: true,
                filesize20MBAll: true
            }
        },
        messages: {
            qa_patch_title: {
                required: "Please enter title.",
                minTrimLength: "Title must be at least 5 characters.",
                noSpace: "Please enter a valid title."
            },
            qa_patch_description: {
                required: "Please enter description.",
                checkspace: "Please enter a valid description.",
                minPlainTextLength: "Description must be at least 5 characters."
            },
            show_in_patch: {
                required: "Please select show in patch."
            },
            'qa_patch_platforms[]': {
                required: "Please select at least one platform."
            }
        },
        submitHandler: function () {
            if (typeof window.submitQaPatchDetailsForm === 'function') {
                window.submitQaPatchDetailsForm();
            }
            return false;
        }
    });

    $.validator.addMethod("minDateTime", function (value, element) {
        var selectedDateTime = moment(value, 'HH:mm');
        var currentDateTime = moment().add(59, 'minutes');
        return this.optional(element) || selectedDateTime.isAfter(currentDateTime);
    }, "Please select a date and time at least 1 hour from now.");

    $("#startMaintenanceForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            maintenanceDateTime: {
                required: true,
                minDateTime: true,
            },
        },
        messages: {
            maintenanceDateTime: {
                required: "Please enter date and time",
                minDateTime: "Please select a date and time at least 1 hour from now"
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        },
        focusInvalid: false
    });
    $("#createCrmForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            society_crm_id: {
                required: true,
            },
            crm_limit: {
                required: true,
                digits: true,

                remote: {
                    url: "./controller/buildingController.php",
                    type: "post",

                    data: {
                        checkCrmLimit: true,
                        csrf: csrf,
                        crm_limit: function () {
                            return $("#crm_limit").val();
                        },
                        society_id: function () {
                            return $("#society_id").val();
                        }
                    }
                }
            }
        },
        messages: {
            society_crm_id: {
                required: "Please select server",
            },
            crm_limit: {
                required: "Please enter CRM Limit",
                digits: "Please enter only numeric values",
                remote: "CRM Limit exceeds allowed limit for the selected society"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        },
    });

    $("#editOfficeForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            office_type: {
                required: true,
                noSpace: true,
                maxlength: 100,
                minlength: 3
            },
            office_contact_one: {
                required: true,
                number: true,
                maxlength: 15
            },
            country_code_one: {
                required: true
            },
            office_contact_two: {
                number: true,
                maxlength: 15
            },
            country_code_two: {
                required: false
            },
            office_email_one: {
                required: true,
                email: true,
                maxlength: 250
            },
            office_email_two: {
                email: true,
                maxlength: 250
            },
            order_no: {
                required: true,
                number: true,
            },
            office_address: {
                required: true,
                maxlength: 255,
                noSpace: true,
            }
        },
        messages: {
            office_type: {
                required: "Please enter the office type",
                noSpace: "No space allowed, and cannot be empty",
            },
            office_contact_one: {
                required: "Please enter a contact number",
                number: "Please enter a valid contact number",
            },
            country_code_one: {
                required: "Please select the country code"
            },
            office_contact_two: {
                number: "Please enter a valid contact number",
            },
            country_code_two: {
                required: "Please select the country code"
            },
            office_email_one: {
                required: "Please enter the email address",
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 250 characters"
            },
            office_email_two: {
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 250 characters"
            },
            order_no: {
                required: "Please enter the order number",
                number: "Order number must be a valid number",
            },
            office_address: {
                required: "Please enter the office address",
                maxlength: "Address cannot exceed 255 characters",
                noSpace: "No space allowed, and cannot be empty",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        },
    });
    $("#editClientForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            client_name: {
                required: true,
                noSpace: true,
                maxlength: 50,
                minlength: 3,
            },
            url: {
                required: true,
                url: true,
                noSpace: true,
                maxlength: 150,
                minlength: 3,
            },
            image: {
                extension: "jpg|jpeg|png"
            },
            order_no: {
                required: true,
                number: true,
                min: 1
            }
        },
        messages: {
            client_name: {
                required: "Please enter the client name",
                noSpace: "No space please and don't leave it empty",
            },
            url: {
                required: "Please enter a URL",
                noSpace: "No space please and don't leave it empty",
                url: "Please enter a valid URL"
            },
            image: {
                extension: "Only JPG, JPEG, and PNG files are allowed"
            },
            order_no: {
                required: "Please enter an order number",
                number: "Order number must be a valid number",
                min: "Order number must be at least 1"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        },
    });
    $("#addClientForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            client_name: {
                required: true,
                noSpace: true,
                maxlength: 50,
                minlength: 3
            },
            url: {
                required: true,
                url: true,
                noSpace: true,
                maxlength: 150,
                minlength: 3
            },
            image: {
                required: true,
                extension: "jpg|jpeg|png"
            },
            order_no: {
                required: true,
                number: true,
                min: 1
            }
        },
        messages: {
            client_name: {
                required: "Please enter the client name",
                noSpace: "No space please and don't leave it empty",
                maxlength: "Client name cannot exceed 50 characters",
                minlength: "Client name must be at least 3 characters long"
            },
            url: {
                required: "Please enter a URL",
                noSpace: "No space please and don't leave it empty",
                url: "Please enter a valid URL",
                maxlength: "URL cannot exceed 150 characters",
                minlength: "URL must be at least 3 characters long"
            },
            image: {
                required: "Please upload an image",
                extension: "Only JPG, JPEG, and PNG files are allowed"
            },
            order_no: {
                required: "Please enter the order number",
                number: "Order number must be a valid number",
                min: "Order number must be at least 1"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#addOfficeForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            office_type: {
                required: true,
                noSpace: true,
                maxlength: 100,
                minlength: 3
            },
            office_contact_one: {
                required: true,
                number: true,
                maxlength: 15
            },
            country_code_one: {
                required: true
            },
            office_contact_two: {
                number: true,
                maxlength: 15
            },
            country_code_two: {
                required: false
            },
            office_email_one: {
                required: true,
                email: true,
                maxlength: 250
            },
            office_email_two: {
                email: true,
                maxlength: 250
            },
            order_no: {
                required: true,
                number: true,
            },
            office_address: {
                required: true,
                maxlength: 255,
                noSpace: true,
            }
        },
        messages: {
            office_type: {
                required: "Please enter the office type",
                noSpace: "No space allowed, and cannot be empty",
            },
            office_contact_one: {
                required: "Please enter a contact number",
                number: "Please enter a valid contact number",
            },
            country_code_one: {
                required: "Please select the country code"
            },
            office_contact_two: {
                number: "Please enter a valid contact number",
            },
            country_code_two: {
                required: "Please select the country code"
            },
            office_email_one: {
                required: "Please enter the email address",
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 250 characters"
            },
            office_email_two: {
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 250 characters"
            },
            order_no: {
                required: "Please enter the order number",
                number: "Order number must be a valid number",
            },
            office_address: {
                required: "Please enter the office address",
                maxlength: "Address cannot exceed 255 characters",
                noSpace: "No space allowed, and cannot be empty",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    /* added by satyajeet start*/
    $("#leaveTypeForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span')); // for select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            country_id: {
                required: true
            },
            leave_type_name: {
                required: true,
                noSpace: true,
                alpha: true,
                minlength: 2,
                maxlength: 40,
                remote: {
                    url: "./controller/leaveController.php",
                    type: "POST",
                    data: {
                        leaveTypeNameCheck: 'leaveTypeNameCheck',
                        csrf: csrf,
                        leave_type_name_check: function () {
                            return $("#leave_type_name").val();
                        },
                        country_id: function () {
                            return $("#country_id").val();
                        },
                        edit_leave_type_id: function () {
                            if ($("#leaveActionType").val() == "editLeaveType") {
                                return $("#leave_type_id").val();
                            } else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        var result = JSON.parse(response);
                        if (result.valid) {
                            return "true";
                        } else {
                            return "false";
                        }
                    }
                }
            },
            leave_type_short_name: {
                noSpace: true,
                maxlength: 10,
                minlength: 1,
                alpha: true
            },
            leave_for: {
                required: true
            },
            no_of_leaves: {
                required: true,
                number: true,
                maxlength: 3
            }
        },
        messages: {
            country_id: {
                required: "Please select a country"
            },
            leave_type_name: {
                required: "Please enter the leave type name",
                noSpace: "Leave type name cannot be empty or contain only spaces",
                alpha: "Only letters, spaces, and hyphens are allowed",
                minlength: "Leave type name must be at least 2 characters long",
                maxlength: "Leave type name must be less than 40 characters",
                remote: "This leave type name already exists. Please choose a different one."
            },
            leave_type_short_name: {
                noSpace: "Short name cannot be empty or contain only spaces",
                maxlength: "Short name must be less than 10 characters",
                minlength: "Short name must be at least 1 character",
                alpha: "Only letters, spaces, and hyphens are allowed"
            },
            leave_for: {
                required: "Please select who the leave type applies to"
            },
            no_of_leaves: {
                required: "Please enter the number of leaves",
                number: "Please enter a valid number",
                maxlength: "The number of leaves must be a 3-digit number"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#addIncomeTaxSlabForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            tax_slab_range_start: {
                required: true,
            },
            tax_slab_range_end: {
                required: true,
            },
            tax_slab_percentage: {
                required: true,
            },
            tax_slab_type: {
                required: true,
            },
            tax_slab_age_group: {
                required: true,
            },
            tax_slab_remark: {
                noSpace: true,
            },
        },
        messages: {
            tax_slab_range_start: {
                required: "Please Enter Range Start Amount",
            },
            tax_slab_range_end: {
                required: "Please Enter Range End Amount",
            },
            tax_slab_percentage: {
                required: "Please Enter Percentage",
            },
            tax_slab_type: {
                required: "Please Select Type",
            },
            tax_slab_age_group: {
                required: "Please Select Age Group",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $("#importIncomeTaxSlabForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());      // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);               // default
            }
        },
        rules: {
            year: {
                required: true,
            },
            "tax_slab_id[]": {
                required: true,
            },

        },
        messages: {
            year: {
                required: "Please Enter Year",
            },
            "tax_slab_id[]": {
                required: "Please Select Income Tax Slab",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    /* added by satyajeet end*/
    //start dharti 14-2-2025
    $("#expenseForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            expense_title: {
                required: true,
                noSpace: true,
                alpha: true,
                maxlength: 40,
                minlength: 2,
                remote: {
                    url: "./controller/expenseController.php",
                    type: "post",
                    data: {
                        expenseTitleCheck: 'expenseTitleCheck',
                        csrf: csrf,
                        expense_title_check: function () {
                            return $("#expense_title").val();
                        },
                        edit_expense_id: function () {
                            if ($("#expense_type_action").val() == "updateExpenseData") {
                                return $("#expense_id").val();
                            } else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        var result = JSON.parse(response);

                        if (result.valid) {
                            return "true";
                        } else {
                            return "false";
                        }
                    }
                }
            }
        },
        messages: {
            expense_title: {
                required: "Please enter the Expense Title",
                noSpace: "No space allowed, and cannot be empty",
                alpha: "Only letters, spaces, and hyphens are allowed",
                maxlength: "Maximum 40 characters allowed",
                minlength: "Minimum 2 characters required",
                remote: "This expense title already exists. Please choose a different one."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#holidayForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span')); // for select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            country_id: {
                required: true
            },
            festival_name: {
                required: true,
                noSpace: true,
                minlength: 2,
                maxlength: 40,
                remote: {
                    url: "./controller/holidayController.php",
                    type: "POST",
                    data: {
                        festivalNameCheck: 'festivalNameCheck',
                        csrf: csrf,
                        festival_name_check: function () {
                            return $("#festival_name").val();
                        },
                        country_id: function () {
                            return $("#country_id").val();
                        },
                        edit_holiday_id: function () {

                            if ($("#holiday_type_action").val() == "editHolidayData") {
                                return $("#holiday_id").val();
                            } else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        var result = JSON.parse(response);
                        return result.valid ? "true" : "false";
                    }
                }
            },
            holiday_date: {
                required: true,
                remote: {
                    url: "./controller/holidayController.php",
                    type: "POST",
                    data: {
                        festivalDateCheck: 'festivalDateCheck',
                        csrf: csrf,
                        holiday_date_check: function () {
                            return $("#holiday_date").val();
                        },
                        country_id: function () {
                            return $("#country_id").val();
                        },
                        edit_holiday_id: function () {

                            if ($("#holiday_type_action").val() == "editHolidayData") {
                                return $("#holiday_id").val();
                            } else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        var result = JSON.parse(response);
                        return result.valid ? "true" : "false";
                    }
                }
            }
        },
        messages: {
            country_id: {
                required: "Please select a country"
            },
            festival_name: {
                required: "Please enter the holiday name",
                noSpace: "Holiday name cannot be empty or contain only spaces",
                minlength: "Holiday name must be at least 2 characters long",
                maxlength: "Holiday name must be less than 40 characters",
                remote: "This holiday name already exists. Please choose a different name."
            },
            holiday_date: {
                required: "Please select the holiday date",
                remote: "This holiday date already exists. Please choose a different date."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    //end dharti 14-2-2025
    // added by jainit 04-02-25
    $("#isOnboardingForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            data_onboarding_date: {
                required: true,
            },
            is_data_onboarding: {
                required: true,
            },
        },
        messages: {
            data_onboarding_date: {
                required: "Please Select Correct Date.",
            },
            is_data_onboarding: {
                required: "Please Select Status.",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addEditTemplateForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            template_name: {
                required: true,
                noSpace: true,
                maxlength: 50,
            },
            template_sub: {
                required: true,
                maxlength: 200,
            },
        },
        messages: {
            template_name: {
                required: "Please Enter Template Name.",
            },
            template_sub: {
                required: "Please Enter Email Subject.",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#scheduleTrainingValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            host_name: {
                required: true,
            },
            training_date: {
                required: true,
            },
            session_id: {
                required: true,
            }
        },
        messages: {
            host_name: {
                required: "Please Select Trainer Name.",
            },
            training_date: {
                required: "Please Select Training Date.",
            },
            session_id: {
                required: "Please Select Session.",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#scheduleModuleValidation").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            priority_id: {
                required: true,
            },
            training_module_name: {
                required: true,
                maxlength: 100,
                checkspace: true,
            },
            session_id: {
                required: true,
            },
            module_type: {
                required: true,
            },
            estimated_minutes: {
                required: true,
                digits: true,
                min: 1
            },
            completion_days: {
                required: function () { return $('#module_type').val() == '1'; },
                digits: true,
                min: 1
            }
        },
        messages: {
            priority_id: {
                required: "Please Select priority.",
            },
            training_module_name: {
                required: "Module Name is required.",
                maxlength: "Module name cannot exceed 100 characters.",
                checkspace: "Content cannot be empty or just spaces.",
            },
            session_id: {
                required: "Please Select Session.",
            },
            module_type: {
                required: "Please Select Module Type.",
            },
            estimated_minutes: {
                required: "Estimated minutes is required.",
                digits: "Please enter a valid number.",
                min: "Please enter a value of at least 1."
            },
            completion_days: {
                required: "Completion days is required for Training modules.",
                digits: "Please enter a valid number.",
                min: "Please enter a value of at least 1."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#attendanceForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            training_password: {
                maxlength: 150,
            },
            training_link: {
                maxlength: 50,
                checkspace: true,
            }
        },
        messages: {
            training_password: {
                maxlength: "Password cannot exceed 150 characters.",
            },
            training_link: {
                maxlength: "Recording link cannot exceed 50 characters.",
                checkspace: "Please Enter Correct recording Link.",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });




    //added by dhruvi raval
    $('#addBatchForm').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            batch_name: {
                required: true,
                maxlength: 50,
                remote: {
                    url: "./check-batch-name.php",
                    type: "POST",
                    data: {
                        batch_name: function () {
                            return $("#batch_name").val();
                        },
                        edit_batch_id: function () {
                            if ($("#editBatch").val() == "editBatch") {
                                return $("#edit_batch_id").val();
                            } else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        var result = JSON.parse(response);

                        if (result.valid) {
                            return "true";
                        } else {
                            return "false";
                        }
                    }
                }
            },
            batch_type: {
                required: true
            },
            training_days: {
                required: true,
                digits: true,
                min: 1,
            },
            participant_name: {
                required: true
            },
        },
        messages: {
            batch_name: {
                required: "Batch name is required.",
                maxlength: "Batch name cannot exceed 50 characters.",
                remote: "This batch name already exists. Please choose a different one."
            },
            batch_type: {
                required: "Please select a batch type."
            },
            training_days: {
                required: "Training days is required.",
                digits: "Please enter a valid number for training days.",
                min: "Training days should be at least 1."
            },
            participant_name: {
                required: "Select participant name."
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $.validator.addMethod("cityPattern", function (value, element) {
        return value.trim() === "" || /^[A-Z][a-zA-Z\s]+$/.test(value);
    }, "City must start with a capital letter.");


    $('#training_setup').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            executive_name: {
                required: true,
            },
        },
        messages: {
            executive_name: {
                required: "Please Select Trainer Name",
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $.validator.addMethod("greaterThanStartTime", function (value, element) {
        var startTime = $("#start_time").val();
        if (!startTime || !value) return true;
        var start = moment(startTime, "hh:mm A");
        var end = moment(value, "hh:mm A");
        return end.isAfter(start);
    }, "End Time must be after Start Time");


    $("#addSessionForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            session_name: {
                required: true,
                maxlength: 40,
                checkspace: true
            },
            start_time: {
                required: true
            },
            end_time: {
                required: true,
                greaterThanStartTime: true
            }
        },
        messages: {
            session_name: {
                required: "Session Name is required",
                maxlength: "Session Name cannot be more than 40 characters"
            },
            start_time: {
                required: "Please select a Start Time"
            },
            end_time: {
                required: "Please select an End Time",
                greaterThanStartTime: "End Time must be after Start Time"
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    // $.validator.addMethod("onlyLetters", function (value, element) {
    //     return /^[A-Za-z]+$/.test(value);
    // }, "Participant Name must contain only letters (no spaces).");

    $("#trainingPriorityForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            priority_name: {
                required: true,
                minlength: 2,
                maxlength: 50,
                onlyLetters: true
            }
        },
        messages: {
            priority_name: {
                required: "Please enter Priority Name.",
                minlength: "Priority Name must be at least 2 characters.",
                maxlength: "Priority Name must not exceed 50 characters.",
                // onlyLetters: "Priority Name must contain only letters (no spaces)."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });



    $("#participantsForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            participant_name: {
                required: true,
                minlength: 2,
                maxlength: 50,
                onlyLetters: true
            }
        },
        messages: {
            participant_name: {
                required: "Please enter Participant Name.",
                minlength: "Participant Name must be at least 2 characters.",
                maxlength: "Participant Name must not exceed 50 characters.",
                onlyLetters: "Participant Name must contain only letters (no spaces)."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#updateCompanySettingsForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            face_attendance_delete_days: {
                required: true,
                maxlength: 4,
            },
            work_report_delete_days: {
                required: true,
                maxlength: 4,
            },
            visit_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            expense_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            chat_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            task_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            circular_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            discussion_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            meeting_attachment_delete_days: {
                required: true,
                maxlength: 4,
            },
            tracking_attachment_delete_months: {
                required: true,
                maxlength: 4,
            }
        },
        messages: {
            face_attendance_delete_days: {
                required: "This field is required.",
            }, work_report_delete_days: {
                required: "This field is required.",
            }, visit_attachment_delete_days: {
                required: "This field is required.",
            }, expense_attachment_delete_days: {
                required: "This field is required.",
            }, chat_attachment_delete_days: {
                required: "This field is required.",
            }, task_attachment_delete_days: {
                required: "This field is required.",
            }, circular_attachment_delete_days: {
                required: "This field is required.",
            }, discussion_attachment_delete_days: {
                required: "This field is required.",
            }, meeting_attachment_delete_days: {
                required: "This field is required.",
            }, tracking_attachment_delete_months: {
                required: "This field is required.",
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });


    $("#addimplementWorkReportForm").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            no_of_call: {
                required: true,
                maxlength: 3,
            },
            no_of_linedup: {
                required: true,
                maxlength: 3,
            },
            "society_id[]": {
                required: true,
            },
            report_desc: {
                required: true,
            },
            report_date: {
                required: true,
                remote: {
                    url: 'ajaxCheckReportAvilable.php',
                    type: 'POST',
                    data: {
                        admin_id: function () {
                            return $('#addimplementWorkReportForm :input[name="bms_admin_id"]').val();
                        },

                        report_type: function () {
                            return $('#addimplementWorkReportForm :input[name="report_type"]').val();
                        },
                        editId: function () {
                            return $('#addimplementWorkReportForm :input[name="editId"]').val();
                        },


                    },
                    dataFilter: function (data) {
                        var response = JSON.parse(data);
                        if (response.status == false) {
                            return false;  // Reject the validation
                        } else {
                            return true;  // Accept the validation    
                        }

                    }
                }
            },
            report_type: {
                required: true,
            },
            admin_id: {
                required: true,
            },

        },
        messages: {
            no_of_call: {
                required: "Please Enter No of calls.",
            },
            no_of_linedup: {
                required: "Please Enter No of lined up calls.",
            },
            "society_id[]": {
                required: "Please select attended company.",
            },
            report_desc: {
                required: "Please Enter Report Description.",
            },
            report_date: {
                required: "Please select a report date.",
                remote: " report already added this date and type."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#closure_date").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            hr_training_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            hr_training_days_from_first_meeting: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            first_hr_training_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            owner_team_leader_training_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session1_training_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session1_data_receive_days_from_training: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session1_data_upload_days_from_receive: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session1_completion_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session2_data_receive_days_from_training: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session2_data_upload_days_from_receive: {
                required: true,
                minlength: 1,
                maxlength: 3
            },
            session2_data_upload_days_from_closure: {
                required: true,
                minlength: 1,
                maxlength: 3
            }
        },
        messages: {
            hr_training_days_from_closure: {
                required: "Please enter the HR Training Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            hr_training_days_from_first_meeting: {
                required: "Please enter the HR Training Days from First Meeting.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            first_hr_training_days_from_closure: {
                required: "Please enter the First HR Training Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            owner_team_leader_training_days_from_closure: {
                required: "Please enter the Owner, Team, Leader Training Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session1_training_days_from_closure: {
                required: "Please enter the Session 1 Training Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session1_data_receive_days_from_training: {
                required: "Please enter the Session 1 Data Receive Days from Training.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session1_data_upload_days_from_receive: {
                required: "Please enter the Session 1 Data Upload Days from Receive.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session1_completion_days_from_closure: {
                required: "Please enter the Session 1 Completion Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session2_data_receive_days_from_training: {
                required: "Please enter the Session 2 Data Receive Days from Training.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session2_data_upload_days_from_receive: {
                required: "Please enter the Session 2 Data Upload Days from Receive.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            },
            session2_data_upload_days_from_closure: {
                required: "Please enter the Session 2 Data Upload Days from Closure.",
                minlength: "The value must be at least 1 character long.",
                maxlength: "The value must be no longer than 3 characters."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $('#addPlanForm').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // select2 support
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            plan_name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                noSpace: true
            },
            plan_value: {
                required: true,
                digits: true,
                maxlength: 3,
                minlength: 1,
                remote: {
                    url: 'ajaxCheckPlanAvailable.php',
                    type: 'POST',
                    data: {
                        plan_value: function () {
                            return $('#addPlanForm :input[name="plan_value"]').val();
                        },
                        editId: function () {
                            return $('#addPlanForm :input[name="editId"]').val(); // Hidden input if edit
                        }
                    },
                    dataFilter: function (data) {
                        var response = JSON.parse(data);
                        return response.status;
                    }
                }
            }
        },
        messages: {
            plan_name: {
                required: "Please enter the plan name",
                minlength: "Plan name must be at least 3 characters long",
                maxlength: "Plan name not more then 50 characters long",
                noSpace: "No space please and don't leave it empty",
            },
            plan_value: {
                required: "Please enter no of months",
                number: "Please enter a valid number",
                min: "Plan value must be a positive number",
                remote: "Plan for this no of months already exists."
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $('#suggestionForm').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // support for select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            suggestion_name: {
                required: true,
                minlength: 3,
                noSpace: true
            },
            suggestion_type: {
                required: true
            }
        },
        messages: {
            suggestion_name: {
                required: "Please enter the suggestion name",
                minlength: "Suggestion must be at least 3 characters",
                noSpace: "No space please and don't leave it empty"
            },
            suggestion_type: {
                required: "Please select the suggestion type"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
    $('#refundForm').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // support for select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            refund_amount: {
                required: true,
                minlength: 1,
                maxlength: 10
            },
            refund_description: {
                required: true,
                minlength: 3,
                maxlength: 200,
                noSpace: true
            },
            refund_person_id: {
                required: true
            }
        },
        messages: {
            refund_amount: {
                required: "Please enter refund amount",
                minlength: "Refund amount must be at least 1 digit",
                maxlength: "Refund amount cannot exceed 10 digits"
            },
            refund_description: {
                required: "Please enter refund description",
                minlength: "Description must be at least 3 characters",
                maxlength: "Description cannot exceed 200 characters"
            },
            refund_person_id: {
                required: "Please select refund person"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $("#addmycoWhiteLable").validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            master_company_id: {
                required: true,
                maxlength: 40
            },
            society_name: {
                required: true,
                noSpace: true,
                maxlength: 50
            },
            project_type: {
                required: true
            },
            sub_domain: {
                required: true,
                url: true,
                remote: {
                    url: "./controller/mycoWhitelabelController.php",
                    type: "POST",
                    data: {
                        subDomainCheck: "subDomainCheck",
                        csrf: function () {
                            return $("input[name='csrf']").val();
                        },
                        sub_domain_check: function () {
                            var val = $("#sub_domain").val();
                            return val;
                        },
                        edit_society_id: function () {
                            if ($("#society_id") != undefined && $("#society_id") != '') {
                                var id = $("#society_id").val();
                                return id;
                            }
                            else {
                                return '';
                            }
                        }
                    },
                    dataFilter: function (response) {
                        console.log("Remote Response:", response);
                        var result = JSON.parse(response);
                        return result.valid ? "true" : "false";
                    }
                }
            }
        },
        messages: {
            master_company_id: {
                required: "Please enter master company id",
                maxlength: "Master company id cannot exceed 40 numbers"
            },
            society_name: {
                required: "Please enter company name",
                noSpace: "No spaces are allowed in company name",
                maxlength: "Company name cannot exceed 50 characters"
            },
            project_type: {
                required: "Please select project type"
            },
            sub_domain: {
                required: "Please enter company URL",
                url: "Please enter a valid URL",
                remote: "Domain Name is already exists, please use another Domain name to avoid confusion"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });

    $('#addDeductionRuleFrom').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent()); // radio/checkbox?
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span')); // select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element); // default
            }
        },
        rules: {
            tax_benefit_category_name: {
                required: true,
                noSpace: true
            },
            tax_benefit_year: {
                required: true,
            },
            tax_benefit_order: {
                required: true,
                noSpace: true,
            },
            applicable_for: {
                required: true,
            },
            rule_type: {
                required: true,
            },
            amount_type: {
                required: true,
            },
            max_tax_benefit_amount: {
                required: true,
                noSpace: true,
            },
            amount: {
                required: true,
                noSpace: true,
            },
            formula_modular_1: {
                required: true,
            },
            value: {
                required: true,
                noSpace: true,
            },
            'deduction_head[]': {
                required: true,
            },
            value_1: {
                required: true,
                noSpace: true,
            },
            'deduction_head_1[]': {
                required: true,
            },
            formula_modular_11: {
                required: true,
            },
            value_2: {
                required: true,
                noSpace: true,
            },
            value_3: {
                required: true,
                noSpace: true,
            },
            'deduction_head_2[]': {
                required: true,
            },
            formula_modular_12: {
                required: true,
            },
            'deduction_head_3[]': {
                required: true,
            },
            formula_type: {
                required: true,
            },
            slab_formula_modular_1: {
                required: true,
            },
            max_tax_benefit_amount: {
                required: true,
            },
        },
        messages: {
            tax_benefit_category_name: {
                required: 'Please Enter Tax Benefit Category Name',
            },
            tax_benefit_year: {
                required: 'Please Select Year',
            },
            tax_benefit_order: {
                required: 'Please Enter Tax Benefit Order',
            },
            applicable_for: {
                required: 'Please Select Applicable For',
            },
            rule_name: {
                required: 'Please Enter Rule Name',
            },
            rule_type: {
                required: 'Please Select Rule Type',
            },
            amount_type: {
                required: 'Please Select Amount Type',
            },
            amount: {
                required: 'Please Enter Amount',
            },
            value: {
                required: 'Please Select Value',
            },
            'deduction_head[]': {
                required: 'Please Select Deduction Head',
            },
            value_1: {
                required: 'Please Select Value',
            },
            'deduction_head_1[]': {
                required: 'Please Select Deduction Head',
            },
            value_2: {
                required: 'Please Select Value',
            },
            value_3: {
                required: 'Please Select Value',
            },
            'deduction_head_2[]': {
                required: 'Please Select Deduction Head',
            },
            'deduction_head_3[]': {
                required: 'Please Select Deduction Head',
            },
            formula_type: {
                required: 'Please Select Formula Type',
            },
            formula_modular_1: {
                required: 'Please Select Formula Modular 1',
            },
            formula_modular_11: {
                required: 'Please Select Formula Modular 1',
            },
            formula_modular_12: {
                required: 'Please Select Formula Modular 1',
            },
            slab_formula_modular_1: {
                required: 'Please Select Formula Modular 1',
            },
            max_tax_benefit_amount: {
                required: 'Please Enter Max Amount',
            },
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $('.ajax-loader').show();
            form.submit();
        },
    });


    $('#taxBenefitSubCategoryForm').validate({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('span'));  // support for select2
                element.next('span').addClass('error').removeClass('valid');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            tax_benefit_sub_category_name: {
                required: true,
                minlength: 3,
                maxlength: 150,
                noSpace: true
            },
            tax_benefit_category_id: {
                required: true
            },
            benefit_percent: {
                required: true
            }
        },
        messages: {
            tax_benefit_sub_category_name: {
                required: "Please enter the tax benefit sub category name",
                minlength: "Sub category name must be at least 3 characters",
                maxlength: "Sub category name cannot exceed 150 characters",
                noSpace: "No space please and don't leave it empty"
            },
            tax_benefit_category_id: {
                required: "Please select the category name"
            },
            benefit_percent: {
                required: "Please enter the tax benefit percent"
            }
        },
        submitHandler: function (form) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();
            form.submit();
        }
    });
});


$("#idProofForm").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        country_id: {
            required: true
        },
        id_proof_name: {
            required: true,
            minlength: 2,
            maxlength: 40,
            alpha: true
        },
        id_proof_number_required: {
            required: true
        },
        id_proof_pages: {
            required: true,
            number: true,
        },
        document_min_length: {
            required: true,
            digits: true,
            min: 1,
        },
        document_max_length: {
            required: true,
            digits: true,
            min: 1,
        },
    },
    messages: {
        country_id: {
            required: "Please select a country"
        },
        id_proof_name: {
            required: "Please enter ID Proof Name",
            minlength: "ID Proof Name must be at least 2 characters",
            maxlength: "ID Proof Name cannot exceed 40 characters",
            alpha: "Only letters, spaces, and hyphens are allowed"
        },
        id_proof_number_required: {
            required: "Please select if number is required"
        },
        id_proof_pages: {
            required: "Please enter number of pages",
            number: "Please enter a valid number"
        },
        document_min_length: {
            required: 'Please enter Document Min Length',
            digits: 'Please Enter Number Only',
        },
        document_max_length: {
            required: 'Please enter Document Max Length',
            digits: 'Please Enter Number Only',
        },
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        form.submit();
    }
});


$("#taxLimitForm").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        tax_year: {
            required: true
        },
        new_regime_amount: {
            required: true,
            minlength: 2,
            maxlength: 30
        },
        old_regime_amount: {
            required: true,
            minlength: 2,
            maxlength: 30
        }
    },
    messages: {
        tax_year: {
            required: "Please select tax year"
        },
        new_regime_amount: {
            required: "Please enter new regime amount",
            minlength: "New Regime Amount must be at least 2 characters",
            maxlength: "New Regime Amount cannot exceed 30 characters"
        },
        old_regime_amount: {
            required: "Please enter old regime amount",
            minlength: "Old Regime Amount must be at least 2 characters",
            maxlength: "Old Regime Amount cannot exceed 30 characters"
        }
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        form.submit();
    }
});

$("#feedbackOverdueSettingsForm").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        developer_feedback_overdue_hours: {
            required: true,
            noSpace: true,
        },
        support_feedback_overdue_after_close_hours: {
            required: true,
            noSpace: true,
        },
        support_feedback_overdue_new_ticket_hours: {
            required: true,
            noSpace: true,
        }
    },
    messages: {
        developer_feedback_overdue_hours: {
            required: "Please enter developer feedback overdue hours",
        },
        support_feedback_overdue_after_close_hours: {
            required: "Please enter support feedback overdue after close hours",
        },
        support_feedback_overdue_new_ticket_hours: {
            required: "Please enter support feedback overdue new ticket hours",
        }
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        form.submit();
    }


});
function getIgnoredElements() {
    let ignored = [];
    let allRows = $('#holidaysTable').DataTable().rows().nodes();

    $(allRows).each(function () {
        const row = $(this);
        const isChecked = row.find('input[type="checkbox"]').is(':checked');

        if (!isChecked) {
            row.find('input, select, textarea').each(function () {
                ignored.push(this);
            });
        }
    });

    return ignored;
}

$("#holidayYearForm").validate({
    ignore: [],
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
        let formData = new FormData();
        let allRows = $('#holidaysTable').DataTable().rows().nodes();

        $(allRows).each(function () {
            const row = $(this);
            const isChecked = row.find('input[type="checkbox"]').is(':checked');

            if (isChecked) {
                row.find('input, select, textarea').each(function () {
                    const input = $(this);
                    if (input.attr('name')) {
                        if (input.attr('type') === 'checkbox' || input.attr('type') === 'radio') {
                            if (input.is(':checked')) {
                                formData.append(input.attr('name'), input.val());
                            }
                        } else {
                            formData.append(input.attr('name'), input.val());
                        }
                    }
                });
            }
        });
        formData.append('csrf', csrf);
        formData.append('copyMultipleHolidays', 'copyMultipleHolidays');

        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();

        $.ajax({
            url: $(form).attr('action'),
            type: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".ajax-loader").hide();
                $(':input[type="submit"]').prop('disabled', false);
                if (response == "1") {
                    swal("Holidays Inserted Successfully!", {
                        icon: "success",
                    }).then(() => {
                        window.location.href = "../apAdmin/holidays";
                    });
                } else {
                    swal("Please Select Atleast One Holiday", {
                        icon: "error",
                    });
                }
            },
            error: function (err) {
                $(".ajax-loader").hide();
                $(':input[type="submit"]').prop('disabled', false);
                swal("Error Occuring In Holiday Insertion", {
                    icon: "error",
                });
            }
        });

    }
});
$('#insertSelectedHolidaysBtn').on('click', function (e) {
    e.preventDefault();

    let allRows = $('#holidaysTable').DataTable().rows().nodes();

    $(allRows).each(function () {
        const row = $(this);
        const checkbox = row.find('input[type="checkbox"]');
        const dateInput = row.find('input[name^="holiday_date"]');

        if (checkbox.is(':checked')) {
            dateInput.rules("add", {
                required: true,
                date: true,
                uniqueDateInSelected: true
            });
        } else {
            dateInput.rules("remove", "uniqueDateInSelected");
        }
    });

    let validator = $("#holidayYearForm").data('validator');
    validator.settings.ignore = getIgnoredElements();

    if ($("#holidayYearForm").valid()) {
        $("#holidayYearForm").submit();
    }
});

$.validator.addMethod("uniqueDateInSelected", function (value, element) {
    let isValid = true;
    let seenDates = [];
    let current = element;
    let allRows = $('#holidaysTable').DataTable().rows().nodes();

    $(allRows).each(function () {
        const row = $(this);
        const isChecked = row.find('input[type="checkbox"]').is(':checked');
        const dateInput = row.find('input[name^="holiday_date"]');

        if (isChecked && dateInput.length > 0) {
            const dateVal = dateInput.val();
            if (dateVal) {
                if (seenDates.includes(dateVal)) {
                    if (dateInput[0] === current) {
                        isValid = false;
                    }
                } else {
                    seenDates.push(dateVal);
                }
            }
        }
    });
    return isValid;
}, "Holiday date must be unique in selected holidays.");

$("#addrequestcrmvalidation").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        company_name: {
            required: true
        },
        crm_package_id: {
            required: true,
        },
        crm_plan_expiring_date: {
            required: true,
        },
        crm_limit: {
            required: true,
            digits: true,

            remote: {
                url: "./controller/buildingController.php",
                type: "post",

                data: {
                    checkCrmLimit: true,
                    csrf: csrf,
                    crm_limit: function () {
                        return $("#crm_limit").val();
                    },
                    society_id: function () {
                        return $("#society_id").val();
                    }
                }
            }
        },
        amountReceivedType: {
            required: true,
        },
        amountReceived: {
            required: true,
            minlength: 1,
            maxlength: 10,
        },
        payment_mode: {
            required: true
        },
        per_employee_price: {
            required: true
        }
    },
    messages: {
        company_name: {
            required: "Please select company name"
        },
        crm_package_id: {
            required: "Please select plan"
        },
        crm_plan_expiring_date: {
            required: "Please enter plan expiring date"
        },
        crm_limit: {
            required: "Please enter CRM limit",
            digits: "Please enter only numeric values",
            remote: "CRM limit exceeds allowed limit for the selected company"
        },
        amountReceivedType: {
            required: "Please select payment status"
        },
        amountReceived: {
            required: "Please enter received amount",
            minlength: "Amount must be at least 1 characters",
            maxlength: "Amount cannot exceed 10 characters"
        },
        payment_mode: {
            required: "Please select payment mode"
        },
        per_employee_price: {
            required: "Please enter employee price"
        }
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        form.submit();
    }
});

$("#topicForm").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());      // radio/checkbox?
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));  // select2
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);               // default
        }
    },
    rules: {
        topic_name: {
            required: true,
            noSpace: true,
            minlength: 2,
            maxlength: 100,
        },
        completion_days: {
            required: true,
            number: true,
            minStrict: 1,
            maxlength: 3,
            max: 999
        },
        next_start_days: {
            required: true,
            number: true,
            minStrict: 0,
            maxlength: 3,
            max: 999
        },
        participant_type: {
            required: true
        }
    },
    messages: {
        topic_name: {
            required: "Please enter topic name",
            noSpace: "Topic name cannot be empty or contain only spaces",
            minlength: "Topic name must be at least 2 characters long",
            maxlength: "Topic name cannot exceed 100 characters",
        },
        completion_days: {
            required: "Please enter completion days",
            number: "Please enter a valid number",
            minStrict: "Completion days must be at least 1",
            maxlength: "Completion days cannot exceed 3 digits",
            max: "Completion days cannot exceed 999"
        },
        next_start_days: {
            required: "Please enter next start days",
            number: "Please enter a valid number",
            minStrict: "Next start days must be at least 0",
            maxlength: "Next start days cannot exceed 3 digits",
            max: "Next start days cannot exceed 999"
        },
        participant_type: {
            required: "Please select participant type"
        }
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        saveTopic();
    }
});

$("#scheduleModuleValidation").validate({
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.next('span'));
            element.next('span').addClass('error').removeClass('valid');
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        training_module_name: {
            required: true,
            noSpace: true,
            minlength: 2,
            maxlength: 100
        },
        module_type: {
            required: true
        },
        priority_id: {
            required: true
        },
        topic_id: {
            required: function (element) {
                return $("#module_type").val() === "1";
            }
        },
        session_id: {
            required: function (element) {
                return $("#module_type").val() === "0";
            }
        },
        session_day_id: {
            required: function (element) {
                return $("#module_type").val() === "0";
            }
        },
        completion_days: {
            required: function (element) {
                return $("#module_type").val() === "1";
            },
            number: true,
            minStrict: 1,
            maxlength: 3
        },
        estimated_minutes: {
            required: true,
            number: true,
            minStrict: 1,
            maxlength: 4
        }
    },
    messages: {
        training_module_name: {
            required: "Please enter module name",
            noSpace: "Module name cannot be empty or contain only spaces",
            minlength: "Module name must be at least 2 characters long",
            maxlength: "Module name cannot exceed 100 characters"
        },
        module_type: {
            required: "Please select module type"
        },
        priority_id: {
            required: "Please select priority"
        },
        topic_id: {
            required: "Please select a topic for training modules"
        },
        session_id: {
            required: "Please select session for setup modules"
        },
        session_day_id: {
            required: "Please select session day for setup modules"
        },
        completion_days: {
            required: "Please enter completion days for training modules",
            number: "Please enter a valid number",
            minStrict: "Completion days must be at least 1",
            maxlength: "Completion days cannot exceed 3 digits"
        },
        estimated_minutes: {
            required: "Please enter estimated minutes",
            number: "Please enter a valid number",
            minStrict: "Estimated minutes must be at least 1",
            maxlength: "Estimated minutes cannot exceed 4 digits"
        }
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        $(".ajax-loader").show();
        form.submit();
    }
});
