define(['marionette'],
	function(Marionette) {
		return Marionette.AppRouter.extend(
		{
			appRoutes :
			{
				'' : 'index',
				'views/list' : 'viewsList',
				'views/map' : 'viewsMap',
				'posts/:id' : 'postDetail',
				'sets' : 'sets',
				'sets/:id' : 'setDetail',
				'login' : 'login',
				'register' : 'register',
				'*path' : 'index'
			}
		});
	});
