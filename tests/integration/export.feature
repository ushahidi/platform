@post @oauth2Skip
Feature: Testing the Export API
	@resetFixture @csvexport
	Scenario: Search All Posts and export the results
		Given that I want to get all "Posts"
		When I request "/posts/export"
		And that the response "Content-Type" header is "text/csv"
		Then the csv response body should have heading:
			"""
			author_email,author_realname,color,completed_stages.0,completed_stages.1,contact,contact_id,contact_type,content,created,form_id,form_name,id,locale,lock.0,lock.1,lock.2,lock.3,lock.4,lock.5,message_id,parent_id,post_date,published_to,sets,slug,source,status,title,type,updated,user_id,"Last Location (point).lat","Last Location (point).lon","Test varchar.0","Test varchar.1",Categories.0,Categories.1,"Geometry test","Second Point.lat","Second Point.lon",Status,Links.0,Links.1,"Person Status","Last Location","Test Field Level Locking 3","Test Field Level Locking 4","Test Field Level Locking 5","A Test Field Level Locking 7","Test Field Level Locking 6"
			"""
		And the csv response body should have 51 columns in row 0
		And the csv response body should have 51 columns in row 1