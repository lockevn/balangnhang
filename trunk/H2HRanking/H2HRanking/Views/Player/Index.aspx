<%@ Page Title="" Language="C#" MasterPageFile="~/Views/Shared/Site.Master" Inherits="System.Web.Mvc.ViewPage" %>

<asp:Content ID="Content1" ContentPlaceHolderID="TitleContent" runat="server">
	Index
</asp:Content>

<asp:Content ID="Content2" ContentPlaceHolderID="MainContent" runat="server">

    <h2>Index</h2>
    
    http://code.google.com/apis/visualization/documentation/gallery/columnchart.html
    
    
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["columnchart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date time');
        data.addColumn('number', 'Old rank');
        data.addColumn('number', 'New rank');
        data.addRows(4);
        data.setValue(0, 0, '2004');
        data.setValue(0, 1, 1000);
        data.setValue(0, 2, 400);
        data.setValue(1, 0, '2005');
        data.setValue(1, 1, 1170);
        data.setValue(1, 2, 460);
        data.setValue(2, 0, '2006');
        data.setValue(2, 1, 660);
        data.setValue(2, 2, 1120);
        data.setValue(3, 0, '2007');
        data.setValue(3, 1, 1030);
        data.setValue(3, 2, 540);

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 400, height: 240, is3D: true, title: 'Company Performance'});
      }
    </script>

    <div id="chart_div"></div>


</asp:Content>
