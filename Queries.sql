--Post Query
Select Post From Posts Where (Member_ID = 1) and (IsDeleted = 0) and (IsRedeemed = 0)

--Follower Query
SELECT Distinct Members.FirstName, Members.LastName, Follows.Followed_ID As ParentFollowedID, (Select count(Followed_ID) From Follows where Followed_ID = ParentFollowedID) As FollowerCount, FollowDate
FROM Follows,Members
Where (Members.ID = Follows.Followed_ID)
order by FollowerCount desc

--Media Likes Query
SELECT Distinct Members.FirstName,Members.LastName, MediaApprovals.Media_ID As ParentMediaID,
(Select count(Media_ID) From MediaApprovals where Media_ID = ParentMediaID) As MediaLikes, MediaApproveDate
FROM Members, MediaApprovals,Media
Where (Members.ID = Media.Member_ID)
And (MediaApprovals.Media_ID = Media.ID)
And (MediaApproveDate Between '2016-04-17' and '2016-04-24')
order by MediaLikes desc

