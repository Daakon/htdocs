--Follower of the month query
SELECT Distinct Followed_ID As ParentFollowedID, (Select count(Followed_ID) From Follows where Followed_ID = ParentFollowedID) As FollowerCount, FollowDate
FROM Follows
Where (FollowDate Between '2016-03-20' and '2016-03-29')
order by FollowerCount desc