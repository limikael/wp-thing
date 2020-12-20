let $=jQuery;

let thingResponse;
let thingChart;

async function thingRenderChart() {
	if (thingChart)
		thingChart.destroy();

	$("#thingChartSelect").attr('disabled',true);
	$("#thingVarSelect").attr('disabled',true);
	$("#thingChartPrev").attr('disabled',true);
	$("#thingChartNext").attr('disabled',true);
	$('#thingSpanLabel').html("");

	let timestamp=thingChartTimestamp;
	thingResponse=await $.ajax(thingAjaxUrl,{
		data: {
			action: "thing_chart_data",
			timestamp: timestamp,
			scope: $("#thingChartSelect").val(),
			var: $("#thingVarSelect").val(),
			postId: thingPostId
		},
		dataType: "json"
	});

	$("#thingChartSelect").attr('disabled',false);
	$("#thingVarSelect").attr('disabled',false);
	$("#thingChartPrev").attr('disabled',false);
	$("#thingChartNext").attr('disabled',false);
	$('#thingSpanLabel').html(thingResponse.rangeLabel);

	var ctx = document.getElementById('thingChart').getContext('2d');
	thingChart = new Chart(ctx, {
	    // The type of chart we want to create
	    type: 'line',

	    // The data for our dataset
	    data: {
	        labels: thingResponse.labels,
	        datasets: [{
	            borderColor: 'rgb(255, 99, 132)',
	            data: thingResponse.values,
	            label: $("#thingVarSelect option:selected").text()
	        }]
	    },

	    // Configuration options go here
	    options: {
	    	animation: {
	    		duration: 0
	    	},
	        /*legend: {
	        	display: false
	        },*/
	        scales: {
	        	xAxes: [{
	        		ticks: {
	        			maxTicksLimit: 12
	        		}
	        	}]
	        }
    	},
	});
}

function thingInitChart() {
	$("#thingChartSelect").change(thingRenderChart);
	$("#thingVarSelect").change(thingRenderChart);

	$("#thingChartPrev").click(()=>{
		thingChartTimestamp=thingResponse.prevTimestamp;
		thingRenderChart();
	});

	$("#thingChartNext").click(()=>{
		thingChartTimestamp=thingResponse.nextTimestamp;
		thingRenderChart();
	});

	thingRenderChart();
}

if (document.getElementById("thingChart"))
	thingInitChart();
