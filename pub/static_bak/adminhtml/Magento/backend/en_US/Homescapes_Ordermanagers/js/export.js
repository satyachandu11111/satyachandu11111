define([
    "jquery",
    "jquery/ui",
    "mage/translate"
], function ($) {

    $.widget('mage.ktplExport', {
        options: {},

        _create: function () {
           
            if (this.options.type == 'export') {
                $('#ktpl-export').click(function () {
                        var startDownloadingUrl = this.options.importURL;
                        this.runDownloading(startDownloadingUrl);
                }.bind(this));
            }
          
        },

        error: function (error, processer) {
            if (processer)
                $(processer.parentNode).remove();

        },

        done: function (response) {
            if (response.full_import_done == 1) {
                location.reload();
            }
        },

        runDownloading: function (startDownloadingUrl) {
           console.log('call run downloading function')
           console.log(startDownloadingUrl);
           $("#expor-loader").show();
          
            $.ajax({
                url: startDownloadingUrl,
                type: 'POST',
                dataType: 'json',
                data: {isAjax: true}
            }).done($.proxy(function (response) {
                if (response.success == 'done') {
                   console.log(response.csv_file);
                   var csvUrl=response.csv_file;
                   $("#download-file").attr("href", csvUrl);
                   $("#expor-loader").hide();
                   $("#download-file").show();
                } else if (response.error) {
                    alert(response.message);
                }
            }));
        },

      
    });
    return $.mage.ktplExport;
});
