<html>
	<body>
		
	<ul class="authorize_options">
    <li>
        <form action="<?php echo Request::current()->url() . URL::query() ?>" method="post">
            <input id="authorizeButton" type="submit" class="button authorize" value="Yes, I Authorize This Request" />
            <input type="hidden" name="authorize" value="1" />
        </form>
    </li>
    <li class="cancel">
        <form id="cancel" action="" method="post">
            <input id="cancelButton" type="submit" class="button cancel" value="Cancel" />
            <input type="hidden" name="authorize" value="0" />
        </form>
    </li>
</ul>
</body>
</html>