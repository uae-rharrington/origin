<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Company\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Company\Test\Page\CompanyUsers;

/**
 * Assert that correct success message is displayed.
 */
class AssertCustomerDeletedMessage extends AbstractConstraint
{

    /**
     * Success message.
     *
     * @var string
     */
    private $successMessage = 'The customer was successfully deleted.';

    /**
     * Assert that correct success message is displayed
     *
     * @param CompanyUsers $companyUsers
     */
    public function processAssert(CompanyUsers $companyUsers)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $this->successMessage,
            $companyUsers->getMessages()->getSuccessMessage(),
            'Success message is not correct.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Correct success message is displayed.';
    }
}
