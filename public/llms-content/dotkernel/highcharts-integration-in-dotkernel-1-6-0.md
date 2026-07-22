---
title: "Highcharts Integration in DotKernel 1.6.0"
description: "DotKernel 1.6.0 integrates the Highcharts charting library, adding interactive pie, column and line chart samples to the admin."
author: "deddu"
date_published: "2012-05-18"
canonical_url: "https://www.dotkernel.com/dotkernel/highcharts-integration-in-dotkernel-1-6-0/"
category: "Dotkernel"
language: "en"
---

# Highcharts Integration in DotKernel 1.6.0

## TL;DR
DotKernel 1.6.0 integrates the Highcharts charting library, offering a new, intuitive and interactive charting experience.
Sample charts (pie, column and line) were added to the admin, and the library ships in the project's externals directory.

## What was added

The admin includes samples built with Highcharts:

- A pie chart (with a small custom feature)
- A column chart
- A line chart

The Highcharts library itself can be found in the **externals** directory of the project.

## Example: line chart configuration

The article shows a sample of how a line chart is configured with Highcharts, including the chart type, tooltip formatter, axis labels and the `timeActivity` series:

```javascript
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
            return '<strong>' + this.series.name + ' ' + this.x + '</strong>' + 'Total logins: ' + this.y;
        }
    },
    yAxis: {
        title: {
            text: 'Logins count'
        },
        min: 0
    },
    xAxis: {
        categories: ,
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

**Q: What charting library was integrated in DotKernel 1.6.0?**
A: Highcharts was integrated in DotKernel 1.6.0, offering a new, intuitive and interactive charting experience.

**Q: What sample charts were added to the admin?**
A: The admin includes samples made with Highcharts: a pie chart (with a small custom feature), a column chart, and a line chart.

**Q: Where can the Highcharts library be found in a DotKernel project?**
A: The Highcharts library is located in the externals directory of the project.
