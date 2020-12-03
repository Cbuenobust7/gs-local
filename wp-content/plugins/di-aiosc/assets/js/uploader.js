var aioscUploader = function(params) {
    var main = this;
    main.params = jQuery.extend({
        inputName: 'attachments[]',
        ul: 'ul.aiosc-uploader-files',
        maxFileNameLen: 20,
        maxFiles: 2 //0 == unlimited
    },params);

    main.ul = jQuery(main.params.ul);
    main.ul.addClass('aiosc-uploader-files');
    main.inputName = main.params.inputName;
    main.uploading = false;
    main.uploadHidden = false;
    main.currentInput = null;
    main.currentInputData = {
        name: null
    };

    var fileNames = [];
    var currLength = 0;
    function strip_file_name(file_name) {
        var ml = main.params.maxFileNameLen;
        if(ml < 1 || file_name.length <= ml + 3) return file_name;
        else {
            var half = Math.round(ml / 2);
            var second_half = ml - half;
            var name = file_name.substr(0,half);
            name = name + "...";
            name =  name + file_name.substr(file_name.length - second_half,second_half);
            return name;
        }
    }
    function createNew() {
        if(main.uploading == false) {
            currLength = main.ul.find('li').length - 1;
            if((main.params.maxFiles > 0 && main.params.maxFiles > currLength)) {
                var li = jQuery('<li class="aiosc-uploader-file"></li>');
                var input = jQuery('<input type="file" class="aiosc-uploaded-file" name="'+main.inputName+'" />');
                var x = jQuery('<i class="dashicons dashicons-no"></i>');
                var bg = jQuery('<i class="dashicons mainicon dashicons-media-default"></i>');
                var name = jQuery('<span class="aiosc-uploader-file-name"></span>');

                li.append(input).append(x).append(bg).append(name);
                if (jQuery.browser.msie) {
                    // IE suspends timeouts until after the file dialog closes
                    input.on('click',function(e) {
                        setTimeout(function() {
                            if(input.val().length > 0) {
                                if(jQuery.inArray(input.val(), fileNames) < 0) {
                                    fileNames.push(input.val());
                                    name.html(getFileName(input.val()));
                                    li.css('display','none');
                                    main.ul.append(li);
                                    li.fadeIn(200);
                                    currLength++;
                                    if(main.params.maxFiles <= currLength) {
                                        jQuery('li.aiosc-uploader-browse').hide();
                                    }
                                }
                            }
                        }, 0);
                    });
                }
                else {
                    input.on('change', function(e) {
                        if(jQuery.inArray(input.val(), fileNames) < 0) {
                            fileNames.push(input.val());
                            name.html(getFileName(input.val()));
                            li.css('display','none');
                            main.ul.append(li);
                            li.fadeIn(200);
                            currLength++;
                            if(main.params.maxFiles <= currLength) {
                                jQuery('li.aiosc-uploader-browse').hide();
                            }
                        }
                    });
                }
                input.trigger('click');

            }
        }
    }
    function deleteFile(li_element) {
        if(main.uploading == false) {
            var val = li_element.find('input[type="file"]').val();
            var ind = jQuery.inArray(val, fileNames);
            if(ind >= 0) delete fileNames[ind];
            li_element.fadeOut({
                duration: 200,
                complete: function() {
                    li_element.remove();
                    jQuery('li.aiosc-uploader-browse').show();
                }
            });
        }
    }
    main.upload_start = function() {
        main.uploading = true;
    };
    main.upload_end = function() {
        main.uploading = false;
    };
    main.reset = function() {
        main.ul.find('li.aiosc-uploader-file').each(function() {
            deleteFile(jQuery(this));
        })
    };
    function getFileName(value) {
        value = value.split('\\');
        return strip_file_name(value[value.length - 1]);
    }
    jQuery(document).ready(function() {
        jQuery(document).on('click',main.ul.find('li.aiosc-uploader-browse a').selector,function(e) {
            e.preventDefault();
        });
        jQuery(document).on('click',main.ul.find('li.aiosc-uploader-browse').selector,function(e) {
            createNew();
        });
        jQuery(document).on('click','ul.aiosc-uploader-files i.dashicons-no',function(e) {
            e.preventDefault();
            deleteFile(jQuery(this).parent('li'));
        });
    })
};