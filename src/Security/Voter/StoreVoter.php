<?php


namespace App\Security\Voter;


use App\Entity\StoreAccount;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class StoreVoter implements VoterInterface
{
    /**
     * @inheritDoc
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $loggedStore = $token->getUser();

        if(!$loggedStore instanceof StoreAccount) {
            return self::ACCESS_DENIED;
        }

        $storeRequested = $attributes[0];

        foreach($attributes as $attribute) {
            if($attribute instanceof StoreAccount) {
                if($loggedStore === $storeRequested) {
                    return self::ACCESS_GRANTED;
                }
            }
        }

        return self::ACCESS_ABSTAIN;
    }
}