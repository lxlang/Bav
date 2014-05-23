<?php








/**
 * Implements 87
 *
 * Copyright (C) 2006  Markus Malkusch <markus@malkusch.de>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


class BAV_Validator_87 extends BAV_Validator_Chain
{


    private
    /**
     * @var array
     */
    $defaultValidators = array(),
    /**
     * @var array
     */
    $exceptionValidators = array();


    public function __construct(BAV_Bank $bank)
    {
        parent::__construct($bank);

        $this->defaultValidators[] = new BAV_Validator_87a($bank);
        
        $this->defaultValidators[] = new BAV_Validator_33($bank);
        $this->defaultValidators[1]->setWeights(array(2, 3, 4, 5, 6));
        $this->defaultValidators[1]->setEnd(4);
        
        $this->defaultValidators[] = new BAV_Validator_87c($bank);
        
        $this->exceptionValidators = BAV_Validator_51::getExceptionValidators($bank);
    }
    
    
    /**
     */
    protected function init($account)
    {
        parent::init($account);
        
        $this->validators = $this->account{2} == 9
                          ? $this->exceptionValidators
                          : $this->defaultValidators;
    }


}


?>