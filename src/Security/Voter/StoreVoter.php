<?php


namespace App\Security\Voter;


use App\Entity\StoreAccount;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StoreVoter extends Voter
{
    const ACCESS = "access";

    protected function supports(string $attribute, $subject)
    {
        $validAccess = [self::ACCESS];

        if(!$subject instanceof StoreAccount) {
            return false;
        }

        if(!in_array($attribute, $validAccess)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $loggedStore = $token->getUser();

        if(!$loggedStore instanceof StoreAccount) {
            return false ;
        }

        $storeRequested = $subject;

        if($loggedStore === $storeRequested) {
            return true;
        }

        return false;
    }
}