// get data to feed chart
var created = [],
  modified = [],
  learned = [],
  forgotten = [];

$.ajax({
    type: "GET",
    url: "db/getstats.php",
    async: false,
    //data: "data",
    dataType: "json"
  })
  .done(function (data) {
    //alert(data);
    created = data['created'];
    reviewed = data['modified'];
    learned = data['learned'];
    forgotten = data['forgotten'];
  })
  .fail(function () {

  });

// build chart
// color scheme: { blue: new; green: learned; yellow: learning; red: relearning }
var ctx = document.getElementById("myChart").getContext("2d");
var myChart = new Chart(ctx, {
  type: "bar",
  data: {
    labels: ["6 days ago", "5 days ago", "4 days ago", "3 days ago", "2 days ago", "Yesterday", "Today"],
    datasets: [{
        label: "New",
        data: created,
        backgroundColor: "rgba(33,150,243,0.4)" // blue
      },
      {
        label: "Reviewed",
        data: reviewed,
        backgroundColor: "rgba(255,235,59,0.4)" // yellow
      },
      {
        label: "Learned",
        data: learned,
        backgroundColor: "rgba(76,175,80,0.4)" // green
      },
      {
        label: "Forgotten",
        data: forgotten,
        backgroundColor: "rgba(244,67,54,0.4)" // red
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'Your progress this week'
    },
    scales: {
      yAxes: [{
        scaleLabel: {
          display: true,
          labelString: 'Number of words',
        }
      }]
    }
  }
});