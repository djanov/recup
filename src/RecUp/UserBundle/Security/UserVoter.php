<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/27/2016
 * Time: 6:38 PM
 */

namespace RecUp\UserBundle\Security;

use RecUp\UserBundle\Entity\User;
use RecUp\UserBundle\Entity\UserProfile;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof UserProfile) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUsername();
//       getUser

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var UserProfile $userProfile*/
        $userProfile = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($userProfile, $user);
            case self::EDIT:
                return $this->canEdit($userProfile, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(UserProfile $userProfile, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($userProfile, $user)) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
//        return !$userProfile->isPrivate();
    }

    private function canEdit(UserProfile $userProfile, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $userProfile->getUsername();
    }
}