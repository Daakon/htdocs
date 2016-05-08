--Post Query
Select count(Post) as PostCount
From Posts
Where (Member_ID = X) and (IsDeleted = 0) and (IsRedeemed = 0)

--Post Like Query
Select count(ID) as LikeCount
From PostApprovals
Where (Member_ID = X) and (IsRedeemed = 0)

--Follower Query
SELECT COUNT( Followed_ID ) AS FollowerCount
FROM Follows
WHERE Followed_ID = X
AND IsRedeemed =0
ORDER BY FollowerCount DESC


--Update Redemption Status
Update Posts Set IsRedeemed = 1 Where IsRedeemed = 0 And Member_ID = X

Update PostApprovals Set IsRedeemed = 1 Where IsRedeemed = 0 And Member_ID = X

Update Follows Set IsRedeemed = 1 Where IsRedeemed = 0 And Followed_ID = X

