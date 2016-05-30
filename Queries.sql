--Post Like Query
Select count(ID) as LikeCount
From PostApprovals
Where (Owner_ID = X) and (IsRedeemed = 0)

--Follower Query
SELECT COUNT(ID) AS FollowerCount
FROM Follows
WHERE Followed_ID = X
AND IsRedeemed =0
ORDER BY FollowerCount DESC

--Sign up referrals
Select count(ID) as ReferralCount
From Referrals
Where (Referral_ID = $ID)


--Update Redemption Status
Update PostApprovals Set IsRedeemed = 1 Where IsRedeemed = 0 And Member_ID = X

--Update Follows Set IsRedeemed = 1 Where IsRedeemed = 0 And Followed_ID = X

