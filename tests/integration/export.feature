@post @oauth2Skip
Feature: Testing the Export API
	@resetFixture @csvexport
	Scenario: Search All Posts and export the results
		Given that I want to get all "Posts"
		When I request "/posts/export"
		And that the response "Content-Type" header is "text/csv"
		Then the csv response body should have heading:
			"""
			author_email,author_realname,color,completed_stages.0,completed_stages.1,contact,contact_id,contact_type,content,created,form_id,form_name,id,locale,lock.0,lock.1,lock.2,lock.3,lock.4,lock.5,message_id,parent_id,post_date,published_to,sets,slug,source,status,title,type,updated,user_id,"Last Location (point).lat","Last Location (point).lon","Test varchar.0","Test varchar.1",Categories,"Geometry test","Second Point.lat","Second Point.lon",Status,Links.0,Links.1,"Person Status","Last Location","Test Field Level Locking 3","Test Field Level Locking 4","Test Field Level Locking 5","A Test Field Level Locking 7","Test Field Level Locking 6"
			"""
		And the csv response body should have 50 columns in row 0
		And the csv response body should have 50 columns in row 1
	Scenario: Search All Posts and export the results
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			format=csv&status%5B%5D=draft&
			"""
		When I request "/posts/export"
		Then the csv response body should equal:
			"""
			author_email,author_realname,color,completed_stages,contact,contact_id,contact_type,content,created,form_id,form_name,id,locale,lock,message_id,parent_id,post_date,published_to,sets,slug,source,status,title,type,updated,user_id,"Last Location"
			,,,,,,,"Testing oauth posts api access",1398774435,1,"Test Form",111,en_us,,,,"2014-04-29 05:27:15",,"Test collection",,,draft,"ACL private post",report,,1,Hamilton
			,,,,,,,"Testing draft 2",1404996921,1,"Test Form",113,en_us,,,,"2014-07-10 05:55:21",,"Test collection",,,draft,"Draft 2",report,,,
			,,,,,,,"Testing draft",1408607443,1,"Test Form",112,en_us,,,,"2014-08-21 00:50:43",,"Test collection",,,draft,"Draft 1",report,,,
			,,,,,,,"Update for draft",1412005016,1,"Test Form",117,en_us,,,110,"2014-09-29 08:36:56",,,,,draft,"Update for draft",report,,,
			,,,,123456789,1,phone,"Post to test inbound messages",1412025016,1,"Test Form",1693,en_us,,10,,"2014-09-29 14:10:16",,,,sms,draft,"Test inbound message",report,,1,
			"""