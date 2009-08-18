<html>
<head>

<title>My first web service page</title>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("language","1");

function init () {
        google.language.translate("kitchen", "en", "vi", function (translated) {
        alert(translated.translation);
    });
}

google.setOnLoadCallback(init);
</script>
</head>
<body>

this is
super man
Danhut
smart
web    

long lanh

</body>
</html>