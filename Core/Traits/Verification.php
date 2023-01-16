<?php
namespace Core\Traits;

trait Verification
{
       /**
     * Verification status
     * @return boolean
     */
    public function verified(): bool
    {
        if ($this->verified == VERIFIED) return true;
        return false;
    }

    /**
     * Approved status 
     * @param User $user
     * @return boolean
     */
    public function approved(): bool
    {
        if ($this->approved_at == NOT_VERIFIED || empty($this->approved_at)) return false;
        return true;
    }

    /**
     * Avatar status
     * method checks if user avater is not empty or 
     * not in step one
     * @return boolean
     */
    public function uploadedAvater(): bool
    {
        # if start_up is STEP1 or image is not uploaded
        if ($this->start_up > STEP1 && !empty($this->avater) || !empty($this->avater)) return true;
        return false;
    }

    /**
     * Avatar status
     * @return boolean
     */
    public function takenSnapshot(): bool
    {
        if ($this->start_up > STEP2 && !empty($this->snapshot) || !empty($this->snapshot)) return true;
        return false;
    }

    /**
     * Profile information uploaded
     * @return boolean
     */
    public function uploadedInformation(): bool
    {
        if ($this->start_up > STEP3 && !empty($this->snapshot) && !empty($this->avater)) return true;
        return false;
    }

    /**
     * 
     */
    public function verifyAvatar()
    {
        return $this->put(['start_up' => STEP2]);
    }
    /**
     * 
     */
    public function verifySnapshot()
    {
        return $this->put(['start_up' => STEP3]);
    }
}