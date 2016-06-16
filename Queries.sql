--Post Like Query
Select count(PostApprovals.ID) as LikeCount
From PostApprovals, Members
Where (PostApprovals.Owner_ID = X) and (PostApprovals.IsRedeemed = 0)
And PostApprovals.Member_ID = Members.ID
And Members.IsEmailValidated = 1

--Update Post Approvals
Update PostApprovals
Join Members
ON PostApprovals.Member_ID = Members.ID
Set IsRedeemed = 1
Where IsRedeemed = 0 And Owner_ID = X

--------------------------------------------------------------------------------
--Comments
Select Count(PostComments.ID) As CommentCount
FROM PostComments, Members
WHERE PostComments.Owner_ID = 'X' And PostComments.IsRedeemed = 0
And PostComments.Member_ID = Members.ID
And Members.IsEmailValidated = 1

--update follower count
Update PostComments
Join Members
ON PostComments.Member_ID = Members.ID
Set IsRedeemed = 1 Where Owner_ID = X

--------------------------------------------------------------------------------
--Follower Query
SELECT COUNT(Follows.ID) AS FollowerCount
FROM Follows, Members
WHERE Follows.Followed_ID = X
AND IsRedeemed =0
And Follows.Follower_ID = Members.ID
And Members.IsEmailValidated = 1

--Update Follows
UPDATE Follows SET IsRedeemed =1 WHERE Followed_ID = X and IsRedeemed = 0

--------------------------------------------------------------------------------

--Sign up referrals
--Join Members table to make sure member has validated their email
SELECT COUNT( Referrals.ID ) AS ReferralCount
FROM Referrals, Members
WHERE Referrals.Referral_ID =  '$username'
AND Referrals.IsRedeemed =0
AND Referrals.Signup_ID = Members.ID
AND Referrals.Signup_ID
IN (
SELECT Posts.Member_ID
FROM Posts, Referrals
WHERE Posts.Member_ID = Referrals.Signup_ID
AND Posts.IsDeleted =0
)
AND Members.IsEmailValidated =1

--Update Referrals
UPDATE Referrals
       JOIN Members
       ON Referrals.Signup_ID = Members.ID
SET    Referrals.IsRedeemed = 1
WHERE Referrals.Referral_ID = 'X' and Members.IsEmailValidated = 1



