<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="/sign/online" method="post">
    {{csrf_field()}}
    字段1: <input type="text" name="k[]">  字段值: <input type="text" name="v[]"><br>
    字段2: <input type="text" name="k[]">  字段值: <input type="text" name="v[]"><br>
    字段3: <input type="text" name="k[]">  字段值: <input type="text" name="v[]"><br>
    字段4: <input type="text" name="k[]">  字段值: <input type="text" name="v[]"><br>
    签名(base64encode):<br><textarea name="sign" id="" cols="30" rows="10"></textarea>
    <input type="submit" value="提交">
</form>
</body>
</html>