---
title: "Highcharts Integration in Dotkernel 1.6.0"
description: "Dotkernel 1.6.0 integrates the Highcharts charting library, adding interactive pie, column and line chart samples to the admin."
author: "deddu"
date_published: "2012-05-18"
canonical_url: "https://www.dotkernel.com/dotkernel/highcharts-integration-in-dotkernel-1-6-0/"
category: "Dotkernel"
language: "en"
---

# Highcharts Integration in Dotkernel 1.6.0

## TL;DR
Dotkernel 1.6.0 integrates the Highcharts charting library, offering a new, intuitive and interactive charting experience.
Sample charts (pie, column and line) were added to the admin, and the library ships in the project's externals directory.

Integrating a new charting library in the latest version of Dotkernel (1.6.0) we offer a new experience with this new intuitive and interactive charts.

Also in admin we made some samples using highcharts. These samples includes an pie chart (with a small custom feature), an column chart and the last one is an line chart.

[![](/uploads/article/019f8a80-cc3b-71e4-913b-5c28321fc438/highcharts-1024x651.png)](/uploads/2012/05/highcharts.png)

You can find highcharts library in the **externals **directory.

Take a quick view on the code to see how highcharts are working.

```
chart = new Highcharts.Chart({
        chart: {
            renderTo: elementId,
            type: 'line',
            plotBackgroundColor: null,
            plotBorderWidth: 0,
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        colors: colors,
        tooltip: {
            formatter: function() {
                    return '' + this.series.name + ' ' + this.x + '
' + 'Total logins: ' + this.y;
            }
        },
        yAxis: {
            title: {
                text: 'Logins count'
            },
            min: 0
        },
        xAxis: {
            categories: ['1','2','3','4','5','6','7','8','9','10','11','12',
                                     '13','14','15','16','17','18','19','20','21','22',
                                     '23','24','25','26','27','28','29','30','31'],
            labels: {
                rotation: -45,
                align: 'right',
                style: {
                    font: 'normal 10px Verdana, sans-serif'
                }
            }
        },
        series: timeActivity
    });
```

 

## FAQ

**Q: What charting library was integrated in Dotkernel 1.6.0?**
A: Highcharts was integrated in Dotkernel 1.6.0, offering a new, intuitive and interactive charting experience.

**Q: What sample charts were added to the admin?**
A: The admin includes samples made with Highcharts: a pie chart (with a small custom feature), a column chart, and a line chart.

**Q: Where can the Highcharts library be found in a Dotkernel project?**
A: The Highcharts library is located in the externals directory of the project.
