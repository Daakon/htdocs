--Post Query
Select count(Post) as PostCount
From Posts
Where (Member_ID = 1) and (IsDeleted = 0) and (IsRedeemed = 0)

--Follower Query
SELECT COUNT( Followed_ID ) AS FollowerCount
FROM Follows
WHERE Followed_ID =168
AND IsRedeemed =0
ORDER BY FollowerCount DESC


