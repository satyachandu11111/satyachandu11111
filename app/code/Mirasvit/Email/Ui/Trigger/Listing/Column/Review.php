<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   2.1.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Listing\Column;


class Review extends AbstractColumn
{
    /**
     * {@inheritDoc}
     */
    protected function prepareItem(array $item)
    {
        $value = isset($item['report'][$this->getName()]) && $item['report'][$this->getName()]
            ? $item['report'][$this->getName()]
            : '―';

        //return '<div class="stat-data"><svg class="email__trigger-stat-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M11.304 12.571l1.518 1.536c.196.161.214.482.054.696a.57.57 0 0 1-.429.196.476.476 0 0 1-.357-.161l-1.714-1.732c-.946.5-1.911.732-2.911.732-.982 0-2-.232-2.857-.696-.018-.018-.054 0-.071 0l-1.679 1.696a.54.54 0 0 1-.768 0 .476.476 0 0 1-.161-.357c0-.143.054-.268.161-.375l1.518-1.5c.018-.018.018-.036.018-.054s-.018-.036-.036-.036c-2.107-1.554-3-4.143-2.393-6.714.554-2.268 2.429-4.107 4.732-4.625 2-.446 4.036 0 5.571 1.232 1.518 1.25 2.429 3.054 2.429 5.018v.071a.314.314 0 0 1-.125.25.448.448 0 0 1-.304.054l-.446-.179a.28.28 0 0 1-.143-.232v-.25A5.45 5.45 0 0 0 7.465 1.98c-2.964 0-5.429 2.393-5.482 5.357-.018 1.429.571 2.821 1.589 3.857 1.018 1.054 2.375 1.625 3.857 1.643 1.375 0 2.714-.5 3.732-1.411.071-.071.196-.089.304-.036l.411.161c.089.036.143.125.161.214s-.018.179-.071.232c-.214.196-.429.393-.661.571zm-.375-4.732l-.018-.018c-.143-.071-.214-.214-.161-.357v-.071c0-1.804-1.482-3.232-3.25-3.232-.893 0-1.75.339-2.375.982a3.268 3.268 0 0 0-.893 2.375 3.23 3.23 0 0 0 3.161 3.107h.071c.821 0 1.643-.321 2.268-.893.071-.143.214-.179.357-.107l.036.018c.143.125.196.232.196.321l.036.357c.036.071 0 .179-.089.268-.821.661-1.786 1.036-2.804 1.036a4.324 4.324 0 0 1-3.054-1.25C3.624 9.536 3.196 8.5 3.214 7.393A4.226 4.226 0 0 1 7.41 3.197h.036c2.339 0 4.232 1.839 4.25 4.179v.071l-.446.339a.349.349 0 0 1-.321.054zm3 2.375v.036l.107 1.196c.018.107-.036.214-.125.286a.358.358 0 0 1-.196.054c-.054 0-.089 0-.143-.018l-2.5-1.054c-.125-.071-.214-.179-.214-.286l-.089-.982c0-.018-.018-.036-.018-.036l-2.304-.964s-.036 0-.071.018c-.571.482-1.429.446-1.911-.071a1.446 1.446 0 0 1-.411-1.018c.054-.75.643-1.375 1.357-1.393.375-.018.75.161 1.018.411.286.268.446.643.446 1.018 0 .036 0 .036.036.054l2.25.964.786-.607c.071-.071.214-.071.304-.036l2.589 1.071c.107.071.161.161.161.268a.232.232 0 0 1-.089.232z"></path></svg> '.$value.' </div>';
        return '<span class="fa fa-bullseye"> ' . $value . '</span>';
    }
}
