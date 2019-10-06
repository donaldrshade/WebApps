
$(document).ready( function () {
                 
                  var options = {
	                  animationEnabled: true,
	                  theme: "dark2",
	                  title:{
		                   text: "Project"  // + project number
	                  },
	                  axisY2:{
		                  lineThickness: 0				
	                  },
	                  toolTip: {
		                  shared: true
	                  },
	                  legend:{
		                  verticalAlign: "top",
		                  horizontalAlign: "center"
	                  },
	                  data: [
	                  {     
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Bronze",
		                  axisYType: "secondary",
		                  color: "#cd7f32",
		                  dataPoints: [
			                  
			                  
			                  
			                  { y: 0, label: "India" },
			                  { y: 5, label: "US" },
			                  { y: 3, label: "Germany" },
			                  { y: 6, label: "Brazil" },
			                  { y: 7, label: "China" },
			                  { y: 5, label: "Australia" },
			                  { y: 5, label: "France" },
			                  { y: 7, label: "Italy" },
			                  { y: 2, label: "Singapore" },
			                  { y: 8, label: "Switzerland" },
			                  { y: 1, label: "Japan" }
		                  ]
	                  },
	                  {
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Silver",
		                  axisYType: "secondary",
		                  color: "#C0C0C0",
		                  dataPoints: [
			                  { y: 0, label: "India" },
			                  { y: 1.5, label: "US" },
			                  { y: 1, label: "Germany" },
			                  { y: 2, label: "Brazil" },
			                  { y: 2, label: "China" },
			                  { y: 2.5, label: "Australia" },
			                  { y: 1.5, label: "France" },
			                  { y: 1, label: "Italy" },
			                  { y: 2, label: "Singapore" },
			                  { y: 2, label: "Switzerland" },
			                  { y: 3, label: "Japan" }
		                  ]
	                  },
	                  {
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Gold",
		                  axisYType: "secondary",
		                  color: "#FFD700",
		                  dataPoints: [
			                  { y: 0, label: "India" },
			                  { y: 3, label: "US" },
			                  { y: 3, label: "Germany" },
			                  { y: 3, label: "Brazil" },
			                  { y: 4, label: "China" },
			                  { y: 3, label: "Australia" },
			                  { y: 4.5, label: "France" },
			                  { y: 4.5, label: "Italy" },
			                  { y: 6, label: "Singapore" },
			                  { y: 3, label: "Switzerland" },
			                  { y: 6, label: "Japan" }
			                  ]
	                  },
	
	                  ]
                  };

                  $("#chartContainer").CanvasJSChart(options);
});

