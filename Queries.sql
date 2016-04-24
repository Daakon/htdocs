--Pied Piper Query
SELECT Distinct Members.FirstName, Members.LastName, Follows.Followed_ID As ParentFollowedID, (Select count(Followed_ID) From Follows where Followed_ID = ParentFollowedID) As FollowerCount, FollowDate
FROM Follows,Members
Where (Members.ID = Follows.Followed_ID)
And (FollowDate Between '2016-04-17' and '2016-04-24')
order by FollowerCount desc

--Paprazzi Query
SELECT Distinct Members.FirstName,Members.LastName, MediaApprovals.Media_ID As ParentMediaID,
(Select count(Media_ID) From MediaApprovals where Media_ID = ParentMediaID) As MediaLikes, MediaApproveDate
FROM Members, MediaApprovals,Media
Where (Members.ID = Media.Member_ID)
And (MediaApprovals.Media_ID = Media.ID)
And (MediaApproveDate Between '2016-04-17' and '2016-04-24')
order by MediaLikes desc