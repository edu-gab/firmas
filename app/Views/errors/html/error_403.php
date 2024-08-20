<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }
        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
        }
        p {
            margin-top: 1.5rem;
        }
        .footer {
            margin-top: 2rem;
            border-top: 1px solid #efefef;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }
        a:active,
        a:link,
        a:visited {
            color: #dd4814;
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>403 - File Not Found</h1>

    <p>
        <?php /*if (ENVIRONMENT !== 'production') : */?>
            <?/*= nl2br(esc($message)) */?>
        <?php /*else : */?>
            Sorry! Cannot seem to find the page you were looking for.
        <?php /*endif */?>
    </p>
</div>
</body>
</html>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <script src="//use.fontawesome.com/84c9ca0cf8.js"></script>
    <title>403 Acceso Denegado</title>

    <style type="text/css">
        @import url('//fonts.googleapis.com/css?family=Roboto');
        body {
            background: #E4E7EC;
            color: #000000;
            font: 16px/1.3 "Roboto", sans-serif;
        }
        header {
            width: 100%;
            margin:0px auto;
        }
        h1 {
            text-align: center;
            color:#000000;
            font: 30px/1 "Roboto";
            text-transform: uppercase;
            margin: 5% auto 5%;
            margin-bottom: 35px;
        }

        article { display: block; text-align: center; width: 650px; margin: 10px auto; }

        @media screen and (max-width: 720px) {
            article { display: block; text-align: justify; width: 450px; margin: 0 auto; }
            h1 { font: 70px/1 "Roboto";}
            .wrap {margin-top: 50px;}
        }

        @media screen and (max-width: 480px) {
            article { display: block; text-align: center; width: 300px !important; margin: 0 auto; }
            h1 { font: 50px/1 "Roboto";}
            .wrap {margin-top: 50px;}

        }
    </style>

    <!--[if IE]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

</head>
<body>
<div class="wrap">
    <article>
        <header>
            <h1 id="fittext1">Error 403<i class="fa fa-exclamation-triangle fa-fw"></i></h1>
        </header>
        <p id="fittext2">Acceso Denegado </br> Su usuario <b><?=session()->get('usuario')?></b> no está autorizado para ver este contenido. </br> Por favor contacte al administrador para tener acceso.</p>
    </article>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/FitText.js/1.2.0/jquery.fittext.js"></script>
<script type="text/javascript">
    $("#fittext1").fitText(1.1);
    $("#fittext2").fitText(1.5);
</script>
</body>
</html>