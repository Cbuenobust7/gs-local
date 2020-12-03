var AIOSC_INSTALLER_INDEX = 0;
var AIOSC_INSTALLER_TOTAL = 0;
var dit_prog = 0;
var AIOSC_INSTALLER_PAUSE = false;
var AIOSC_INSTALLER_COMPLETE = false;
var AIOSC_ETA_TIMER = false;
var AIOSC_ELAPSED_TIMER = false;
var AIOSC_ETA_INDEX = 0;
var AIOSC_ELAPSED = 0;
var AIOSC_ETA = 0;
var AIOSC_PER_RUN = 0;
var eta_before = new Date();
(function($) {
    jQuery(document).ready(function($) {
        $(".diwave-pbar").dwProgressBar("init");

        $("#aiosc-installer-start").removeAttr('disabled');
        $(document).on('click','#aiosc-installer-start',function(e) {
            e.preventDefault();
            if(!AIOSC_INSTALLER_COMPLETE) {
                if(AIOSC_INSTALLER_PAUSE == false) {
                    $("#aiosc-progress").html("Starting...<span></span>");
                }
                else {
                    $("#aiosc-progress").html("Resuming...<span></span>");
                    AIOSC_INSTALLER_PAUSE = false;
                }
                $('#aiosc-installer-pause').removeAttr('disabled','disabled').html("Pause");
                $(this).html("Installing...");
                start_elapsed();
                aioscInstallerBegin(AIOSC_INSTALLER_INDEX);
                $(this).attr('disabled','disabled');
            }
            else {
                aiosc_GoToMain();
            }
        });
        $(document).on('click','#aiosc-installer-pause',function(e) {
            AIOSC_INSTALLER_PAUSE = true;
            $(this).attr('disabled','disabled');
            $(this).html("Pausing...");
        });
    });
    function get_elapsed() {
        AIOSC_ELAPSED++;
        $('#aiosc-elapsed').html("&nbsp &nbsp; Elapsed Time: " + secondsToHms(AIOSC_ELAPSED));
    }
    function start_elapsed() {
        if(AIOSC_ELAPSED_TIMER === false) {
            get_elapsed();
            AIOSC_ELAPSED_TIMER = setInterval(get_elapsed, 1000);
        }
    }
    function pause_elapsed() {
        clearInterval(AIOSC_ELAPSED_TIMER);
        AIOSC_ELAPSED_TIMER = false;
    }
    function stop_elapsed() {
        pause_elapsed();
        AIOSC_ELAPSED = 0;
    }
    function get_eta() {
        AIOSC_ETA_INDEX++;
        var runs = Math.round(AIOSC_INSTALLER_TOTAL / AIOSC_PER_RUN) - Math.round(AIOSC_INSTALLER_INDEX / AIOSC_PER_RUN);
        var eta = Math.round(runs * parseFloat(AIOSC_ETA / 1000).toFixed(2)) - AIOSC_ETA_INDEX;
        $('#aiosc-eta').html("&nbsp &nbsp; ETA: " + secondsToHms(eta));
    }
    function start_eta() {
        AIOSC_ETA_INDEX = 0;
        if(AIOSC_ETA_TIMER === false) {
            get_eta();
            AIOSC_ETA_TIMER = setInterval(get_eta, 1000);
        }
    }
    function stop_eta() {
        clearInterval(AIOSC_ETA_TIMER);
        AIOSC_ETA_TIMER = false;
        AIOSC_ETA_INDEX = 0;
        $('#aiosc-eta').html('');
    }
    function secondsToHms(d) {
        d = Number(d);
        if(d < 0) return "0:00";
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        var s = Math.floor(d % 3600 % 60);
        return ((h > 0 ? h + ":" + (m < 10 ? "0" : "") : "") + m + ":" + (s < 10 ? "0" : "") + s);
    }
    function aioscInstallerBegin(index) {
        var pb = $(".diwave-pbar");
        console.log("Sending index: "+AIOSC_INSTALLER_INDEX);
        eta_before = new Date();
        jQuery.post(AIOSC_AJAX_URL, { action: 'aiosc_finalize_activation' ,from: AIOSC_INSTALLER_INDEX, total: AIOSC_INSTALLER_TOTAL },function(data) {
            AIOSC_ETA = new Date() - eta_before;
            console.log(data);
            var res = jQuery.parseJSON(data);
            if(res.result == 2) {
                if(AIOSC_PER_RUN == 0) AIOSC_PER_RUN = res.data.per_run;
                AIOSC_INSTALLER_TOTAL = res.data.total;
                start_eta();
                var last = parseInt(res.data.new_index);
                if(last > 0) {
                    dit_prog += parseFloat(res.data.pb_per_run);
                    $("#aiosc-progress").html("Processing "+res.data.found_total+" users ("+res.data.new_index+" out of "+res.data.total+" processed)... "+dit_prog.toFixed(2)+"%");
                    console.log("Processed "+(res.data.new_index)+" out of "+res.data.total+" users... "+dit_prog+"%");
                    pb.dwProgressBar("value",dit_prog);
                    console.log("DATA: "+last);
                    console.log("FROM: " + AIOSC_INSTALLER_INDEX);
                    if(last > AIOSC_INSTALLER_INDEX) {
                        AIOSC_INSTALLER_INDEX = last;
                        if(!AIOSC_INSTALLER_PAUSE) {
                            aioscInstallerBegin(AIOSC_INSTALLER_INDEX);
                            $('#aiosc-installer-pause').html("Pause");
                        }
                        else {
                            $('#aiosc-installer-pause').html("Pause");
                            $("#aiosc-progress").html("<strong>Paused.</strong> <em>(Refreshing the page will require re-activation)</em>");
                            $("#aiosc-installer-start").removeAttr('disabled').html("Resume");
                            pause_elapsed();
                            stop_eta();
                        }
                    }
                }
            }
            else {
                if(res.result == 1) {
                    AIOSC_INSTALLER_COMPLETE = true;
                    $("#aiosc-progress").html("Activation was completed successfully.");
                    $("#aiosc-installer-start").removeAttr('disabled').html("Finish");
                    $("#aiosc-installer-pause").remove();
                    stop_eta();
                    stop_elapsed();
                    $("#aiosc-global-warning").remove();
                    console.log(res.message);
                }
                else alert(res.message);
            }
        });
    }
})(jQuery);
function aiosc_GoToMain() {
    window.location.href = "admin.php?page=aiosc-list";
}
(function($) {
    $.fn.dwProgressBar = function(func,args) {
        var bar = $(this);
        var indicator = bar.find('.diwave-pbar-indicator');
        var options = $.extend({
            start: 0,
            animate: true,
            speed: 200,
            complete: function() {}
        },args);
        if(func == "init") {
            if(bar.find(".diwave-pbar-indicator").length < 1) {
                indicator = $('<div class="diwave-pbar-indicator" data-value="'+options.start+'"></div>');
                bar.append(indicator);
            }
            move(options.start);
            return bar;
        }
        else if(func == "value" && args == "undefined") {
            return getVal();
        }
        else if(func == "value" && args != "undefined") {
            move(args);
            return bar;
        }
        function getVal() {
            var val = indicator.attr('data-value');
            if(val == "undefined") return 0;
            else return parseInt(val);
        }
        function move(val) {
            if(val > 100) val = 100;
            if(val < 0) val = 0;
            indicator.attr('data-value',val);
            if(options.animate) indicator.stop(true,true).animate({
                "width": val+"%"
            },{
                duration: options.speed,
                complete: options.complete
            });
            else {
                indicator.css("width",val+"%");
                if(typeof options.complete == "function") options.complete.call(bar,getVal());
            }
        }
        return bar;
    }
})(jQuery);