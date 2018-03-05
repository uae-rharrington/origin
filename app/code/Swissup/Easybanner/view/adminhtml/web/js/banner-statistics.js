define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'https://www.gstatic.com/charts/loader.js'
], function($, alert) {
    'use strict';

    var self, url;

    return {
        init: function(ajaxCallUrl, trigger) {
            self = this;
            url = ajaxCallUrl;

            $(trigger).on('change', function() {
                self.generateStatistics($(this).val());
            });
            $(trigger).change();

            google.charts.load('current', {'packages':['corechart']});
        },

        drawChart: function(data) {
            var data = google.visualization.arrayToDataTable(data);

            var options = {
                title: $.mage.__('Banner Statistics'),
                hAxis: {title: '',  titleTextStyle: {color: '#333'}},
                vAxis: {minValue: 0}
            };
            var chart = new google.visualization.AreaChart(
                document.getElementById('banner-statistics')
            );
            chart.draw(data, options);
        },

        generateStatistics: function(type) {
            $.ajax({
                method: "POST",
                url: url,
                showLoader: true,
                dataType: "json",
                data: {
                    form_key: window.FORM_KEY,
                    type: type
                }
            })
            .done(function(data) {
                if (data.error) {
                    alert({
                        title: $.mage.__('Error'),
                        content: data.error
                    });
                    return;
                }
                self.drawChart(data.statistic);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('An error occured:') + errorThrown
                });
            });
        }
    }
});
