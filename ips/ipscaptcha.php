<!DOCTYPE html>
<html>
<head>
    <title>CPSS .:. Всё в порядке, но...</title>
    <meta charset="utf-8" />
    <style type="text/css" >
    	.tbl,.tbl>li{list-style:none}
    	.main,body,html{height:100%}
    	.logo,.tbl>li{vertical-align:middle}
    	h1,h2{font-weight:400}
    	*{margin:0;padding:0;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;line-height:1.5em}
    	body{font-family:"Segoe UI","Helvetica Neue",Helvetica,Tahoma,Arial,sans-serif;background:#e6e7e9}
    	.tbl{display:table;width:100%}.tbl>li{display:table-cell}.main{padding:0 0 84px}
    	.footer{font-size:11px;line-height:18px;padding:24px 12px;height:84px;margin:-84px 0 0;text-align:center}
    	.holder{padding:24px}
    	.content{max-width:1196px;margin:0 auto;width:100%;border:1px solid #ccc;background:#FFF}
    	.head{color:#FFF;background:#0072bc;padding:12px 24px}
    	.head .title{float:right}
    	.head,.head *{line-height:1em!important}
    	.logo{background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAAAkCAYAAABBszIzAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NDkxMSwgMjAxMy8xMC8yOS0xMTo0NzoxNiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzEwMDI5OUI5NjkxMUVBOTFBQ0I3RkI4OUE1MUQ4NSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzEwMDI5QUI5NjkxMUVBOTFBQ0I3RkI4OUE1MUQ4NSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkU3MTAwMjk3Qjk2OTExRUE5MUFDQjdGQjg5QTUxRDg1IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkU3MTAwMjk4Qjk2OTExRUE5MUFDQjdGQjg5QTUxRDg1Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4avgQQAABfdJREFUeNrsnGuIG1UU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) center center no-repeat;width:128px;height:36px;display:inline-block;color:transparent;text-indent:-100%;overflow:hidden}
    	h1{font-size:32px}
    	h2{font-size:28px}
    	h1,h2{text-align:left;margin:0;padding:0}
    	hr{border:none;border-top:1px solid #ccc;height:1px;margin:24px 0}
    	.captcha{margin:12px 0}
    </style>
    <link href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAIAAABuYg/PAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NDkxMSwgMjAxMy8xMC8yOS0xMTo0NzoxNiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTM0QUNCNUNCOTZCMTFFQTg2NDBDRUY0NUU3NjQzNjMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTM0QUNCNUJCOTZCMTFFQTg2NDBDRUY0NUU3NjQzNjMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjUwNjI0ODFFQjk2QjExRUE4NjA2OTY3QTAxNDk1Q0EwIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjUwNjI0ODFGQjk2QjExRUE4NjA2OTY3QTAxNDk1Q0EwIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+7kJMawAABKJJREFUeNq8V2tMU2cYfs+h9Iq1d6AF5LJSHIVqYJsXOl3mSFhkibIpZmNqxlzMsmw6km0/ZsyyZJpplpi4bO6HG7sQNh2Lc6LLzDCMgIDRyUUKSqFAaaU3Sq/09Jx9RV1GC4fT4nxzfpxzvsvzfe/7vM/3fhi82wKPyvBEBmWtaH5zDeSL/2ewZPzFZ7Mm9moHXcGxXQW1FTnASWI+GmPqRiFny1rFkSdTp3xExflhGHWDUtBUmZcr4hzusjZdvwvOwDLAcAx4LJBwS1YJ9+eJNmWuGHIGnv9rAv6emtetUPqLXlUk47eZvV/ddraa3DDlhwABYWppsMpnMt/WyvjJOJeFs5MwigLjdLB+0HmmzwYW36JrVvDKH5fu10jyRFwch9kwFSBIP0F+N+g8fXFkUbD2gyVn7riOG93gC4ErCO5ZNDSuoCKHg4gD/OS9KsE7OrnuaNe/jayozsEwdbzXDndcCbI7RILdH3kATtt8tauldGwMI8dxkx5OWvGidxL9TVHxTyrhQUbKgWxhZgo7TFL2INFmD7SaPUABSQ+GSBFxBRNLYa/VyY+VKDYoUxCb6vvsuy8ZwRkEP3G/Q7YQTUYPhi/I2nkm532oz6grScUw+LTbapoJ7SmUnhhygtkbFRIcw+jAIq00rkzjf6zPeP+JNLufOHx18rOWcXAH39qauwek12x+iAkJRr8z5PRIOsdazspTT2e8ppUGCMobIlMbBsAbQkiopVTOR4kFE55YWSBJig4MTQTcBz9ZOKTyK9Si93QKtYjjm3MLPzmyFKq2qPrSSOPYDKJclVp0EsnKzCzEhISgaIXY7AmtE3PubaVSr6rRyiZC5IleG5uFNxgcDj8x5Quhp9PibeyYRL2OvJCHdnWw2biQnLJdQYJuZz2OwGYZrwO9mdy/jkzD3NJuIk5wkj7ZoJQ0GqDfgXwdUYo0wfc78ndqJKyv+xZU4Ro53+AI0oFdnPIdKpTe49J//395wdjqDA5U5hnKVDY/oRQkF8t4txwB1uc3YMS9IJnKUgW/jbvpwHpNbo1ehdwdK4n97eaCdjPjswsrlnHfmN8/5vCc9EWYny5Yrlal8yVIrkxuWrAw+Ydppm6NYplYr2hl3RYv0vUlyoK669Z9WlmE9wkbBgd0ipfR8b10DTLk8hNh0MkTB8uXILWEHhsDMJKqujLesTkTsASxLmzJ+uCqOVbQF/bV7S4rj4XhpWmJQGllBWLuuTYz41IuTOrOD5u35qJzJD4kdtLodnXp7yNR1Fiqbuyznxuert+hiQvr6DZ1p8Xj6LTEXaTuazSsVwqeQ8FjZoqn0mpWi1/6wZBQRRwg1Kd7z5avgmIGzHxMNLpNrfz2FnhmEy2/JzzChgHXTg1oaCv7bKF9d6Hg7BB9WcYgc3tsop8Gp18thCLZIlkldr1evKnZSHZZ6GdiMYrGjbsrCdJZrdku4f55ZTwqTugoEDUNQbf14d1iem3iUze/KFMdqy64f3Nh4Yeq1J3lOZJv+pkgxXOLeVC+/bhLky/mrmsZu7xR5QgSlQ0GJveXhMDmbKM+4+T69I+uWX++bIpTnx/hNfcfAQYAz+/Vy7OklUAAAAAASUVORK5CYII=" rel="icon" type="image/x-icon">
</head>
<body>

<ul class="tbl main"><li class="holder"><div class="content">

	<ul class="tbl head">
		<li>
			<a href="/cpssinfo" title="CPSS"><span class="logo">CPSS</span></a>
			<h1 class="title">Кто здесь?</h1>
		</li>
	</ul>
	<div class="holder">
		<h1>Все в порядке, но...</h1>
		в целях обеспечения безопасности ресурса нам необходимо убедиться, что вы человек.<br />
		<br />
		Пожалуйста введите защитный код, расположенный ниже и нажмите кнопку "Submit".

		<div class="captcha">
			<form class="challenge-form" id="challenge-form" action="/cdn-cgi/l/chk_captcha" method="get">
  <script type="text/javascript" src="/cdn-cgi/scripts/cf.challenge.js" data-type="normal"  data-ray="3084045a63581491" async data-sitekey="6LfOYgoTAAAAAInWDVTLSc8Yibqp-c9DaLimzNGM" data-stoken="fCcTxN78GMDIGvF8tfr9PiopJC7POgKhVZbkfw85KTT20VrZu4-WGUXh1qKMjNqvYRqs05shG6eUGMgsJO1ASdaMxk3aGHFiOsU6J0reoAc"></script>
  <div class="g-recaptcha"></div>
  <noscript id="cf-captcha-bookmark" class="cf-captcha-info">
    <div><div style="width: 302px">
      <div>
        <iframe src="https://www.google.com/recaptcha/api/fallback?k=6LfOYgoTAAAAAInWDVTLSc8Yibqp-c9DaLimzNGM&stoken=fCcTxN78GMDIGvF8tfr9PiopJC7POgKhVZbkfw85KTT20VrZu4-WGUXh1qKMjNqvYRqs05shG6eUGMgsJO1ASdaMxk3aGHFiOsU6J0reoAc" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
      </div>
      <div style="width: 300px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
        <textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;"></textarea>
        <input type="submit" value="Submit"></input>
      </div>
    </div></div>
  </noscript>
</form>

		</div>

		<hr />

		<h2>Что случилось?</h2>
		<small>
		Этот текст мало кто будет читать и мы можем написать здесь все, что угодно, например...<br />
		Вы живете в неведении. Роботы уже вторглись в нашу жизнь и быстро захватывают мир, но мы встали на светлый путь и боремся за выживание человечества. А если серьезно, то...<br />
		<br />
		В целях обеспечения безопасности сайта от кибератак нам необходимо убедиться, что вы человек.  Если данная страница выводится вам часто, есть вероятность, что ваш компьютер заражен или вы используете для доступа IP адрес зараженных компьютеров.<br />
		<br />
		Если это ваш частный компьютер и вы пытаетесь зайти на сайт, например, из дома - мы рекомендуем вам проверить ваш компьютер на наличие вирусов.<br />
		<br />
		Если вы пытаетесь зайти на сайт, например, с работы или открытых сетей - вам необходимо обратиться с системному администратору и сообщить, что о возможном заражении компьютеров в вашей сети.
		</small>
	</div>

</div></li></ul>

<ul class="tbl footer">
	<li>&copy; 2016-<?php echo date('Y')?>, &laquo;CPSS&raquo;. Система контроля и защиты сайтов.</li>
</ul>

</body>
</html>
