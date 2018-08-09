<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */

/**
 * Scope config Provider model
 */

namespace Amasty\Faq\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const ENABLED = 'amastyfaq/general/enabled';

    const URL_KEY_PATH = 'amastyfaq/general/url_key';

    const LIMIT_SHORT_ANSWER = 'amastyfaq/faq_page/limit_short_answer';

    const USER_NOTIFY = 'amastyfaq/user_email/user_notify';

    const USER_NOTIFY_SENDER = 'amastyfaq/user_email/sender';

    const USER_NOTIFY_EMAIL_TEMPLATE = 'amastyfaq/user_email/template';

    const ADMIN_NOTIFY = 'amastyfaq/admin_email/enable_notify';

    const ADMIN_NOTIFY_EMAIL = 'amastyfaq/admin_email/send_to';

    const ADMIN_NOTIFY_EMAIL_TEMPLATE = 'amastyfaq/admin_email/template';

    const CATEGORIES_SORT = 'amastyfaq/faq_page/category_sort';

    const QUESTIONS_SORT = 'amastyfaq/faq_page/question_sort';

    const SHOW_ASK_QUESTION_FORM_ON_PRODUCT_PAGE = 'amastyfaq/product_page/show_link';

    const SHOW_ASK_QUESTION_FORM_ON_ANSWER_PAGE = 'amastyfaq/faq_page/show_ask';

    const SHOW_BREADCRUMBS = 'amastyfaq/faq_page/show_breadcrumbs';

    const LABEL = 'amastyfaq/general/label';

    const LABEL_NO_RESULT = 'amastyfaq/faq_page/no_result';

    const ADD_TO_MAIN_MENU = 'amastyfaq/general/add_to_category_menu';

    const IS_RATING_ENABLED = 'amastyfaq/rating/enabled';

    const RATING_TEMPLATE = 'amastyfaq/rating/type';

    const IS_SITEMAP_ENABLED = 'amastyfaq/seo/sitemap';

    const CHANGE_FREQUENCY = 'amastyfaq/seo/changefreq';

    const SITEMAP_PRIORITY = 'amastyfaq/seo/sitemap_priority';

    const CANONICAL_URL = 'amastyfaq/seo/canonical_url';

    const ADD_RICHDATA_BREADCRUMBS = 'amastyfaq/seo/add_richdata_breadcrumbs';

    const ADD_RICHDATA_ORGANIZATION = 'amastyfaq/seo/add_richdata_organization';

    const RICHDATA_ORGANIZATION_WEBSITE_URL = 'amastyfaq/seo/organization_website_url';

    const RICHDATA_ORGANIZATION_LOGO_URL = 'amastyfaq/seo/organization_logo_url';

    const RICHDATA_ORGANIZATION_NAME = 'amastyfaq/seo/organization_name';

    const ADD_RICHDATA_CONTACT = 'amastyfaq/seo/add_richdata_contact';

    const RICHDATA_ORGANIZATION_CONTACT_TYPE = 'amastyfaq/seo/organization_contact_type';

    const RICHDATA_ORGANIZATION_TELEPHONE = 'amastyfaq/seo/organization_telephone';

    const SEARCH_PAGE_SIZE = 'amastyfaq/faq_page/limit_question_search';

    const CATEGORY_PAGE_SIZE = 'amastyfaq/faq_page/limit_question_category';

    const PRODUCT_PAGE_SIZE = 'amastyfaq/product_page/limit_question_product';

    const SOCIAL_ACTIVE_BUTTONS = 'amastyfaq/social/buttons';

    const PAGE_LAYOUT = 'amastyfaq/faq_home_page/layout';

    const FAQ_PAGE_SHORT_ANSWER_BEHAVIOR = 'amastyfaq/faq_page/short_answer_behavior';

    const PRODUCT_PAGE_SHORT_ANSWER_BEHAVIOR = 'amastyfaq/product_page/short_answer_behavior';

    const FAQ_CMS_HOME_PAGE = 'amastyfaq/faq_home_page/cmspages_faq_home_page';

    const USE_FAQ_CMS_HOME_PAGE = 'amastyfaq/faq_home_page/use_faq_home_page';

    const GDPR_ENABLED = 'amastyfaq/gdpr/enabled';

    const GDPR_TEXT = 'amastyfaq/gdpr/text';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProvider constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $key
     * @param string $scopeType
     * @param null|string $storeId
     *
     * @return mixed
     */
    public function getValue($key, $scopeType = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeConfig->getValue($key, $scopeType, $storeId);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isEnabled($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return (bool)$this->getValue(self::ENABLED, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getUrlKey($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::URL_KEY_PATH, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return int
     */
    public function getLimitShortAnswer($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::LIMIT_SHORT_ANSWER, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isNotifyUser($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::USER_NOTIFY, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getNotifySender($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::USER_NOTIFY_SENDER, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isNotifyAdmin($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::ADMIN_NOTIFY, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function notifyAdminEmail($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::ADMIN_NOTIFY_EMAIL, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getCategoriesSort($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::CATEGORIES_SORT, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getQuestionsSort($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::QUESTIONS_SORT, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isShowAskQuestionOnAnswerPage($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::SHOW_ASK_QUESTION_FORM_ON_ANSWER_PAGE, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isShowAskQuestionOnProductPage($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::SHOW_ASK_QUESTION_FORM_ON_PRODUCT_PAGE, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isShowBreadcrumbs($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::SHOW_BREADCRUMBS, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getLabel($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::LABEL, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getNoItemsLabel($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::LABEL_NO_RESULT, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isAddToMainMenu($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::ADD_TO_MAIN_MENU, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isRatingEnabled($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::IS_RATING_ENABLED, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRatingTemplateName($scopeType = ScopeInterface::SCOPE_STORE)
    {
        switch ($this->getValue(self::RATING_TEMPLATE, $scopeType)) {
            case \Amasty\Faq\Model\OptionSource\Question\RatingType::VOTING:
                $templateName = 'voting';
                break;
            case \Amasty\Faq\Model\OptionSource\Question\RatingType::YESNO:
            default:
                $templateName = 'yesno';
                break;
        }
        return 'Amasty_Faq/rating/' . $templateName;
    }

    /**
     * @param string $scopeType
     * @param $storeId
     *
     * @return bool
     */
    public function isSiteMapEnabled($scopeType = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return (bool)$this->getValue(self::IS_SITEMAP_ENABLED, $scopeType, $storeId);
    }

    /**
     * @param string $scopeType
     * @param $storeId
     *
     * @return string
     */
    public function getFrequency($scopeType = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->getValue(self::CHANGE_FREQUENCY, $scopeType, $storeId);
    }

    /**
     * @param string $scopeType
     * @param $storeId
     *
     * @return string
     */
    public function getSitemapPriority($scopeType = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->getValue(self::SITEMAP_PRIORITY, $scopeType, $storeId);
    }

    /**
     * @param string $scopeType
     * @param null   $storeId
     *
     * @return bool
     */
    public function isCanonicalUrlEnabled($scopeType = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return (bool)$this->getValue(self::CANONICAL_URL, $scopeType, $storeId);
    }

    /**
     * @return array
     */
    public function getSocialActiveButtons()
    {
        return explode(',', $this->getValue(self::SOCIAL_ACTIVE_BUTTONS));
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isAddRichDataBreadcrumbs($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_BREADCRUMBS, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isAddRichDataOrganization($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_ORGANIZATION, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRichDataWebsiteUrl($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_WEBSITE_URL, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRichDataLogoUrl($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_LOGO_URL, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRichDataOrganizationName($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_NAME, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isAddRichDataContact($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::ADD_RICHDATA_CONTACT, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRichDataContactType($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_CONTACT_TYPE, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getRichDataTelephone($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::RICHDATA_ORGANIZATION_TELEPHONE, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return int
     */
    public function getProductPageSize($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::PRODUCT_PAGE_SIZE, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return int
     */
    public function getCategoryPageSize($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::CATEGORY_PAGE_SIZE, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return int
     */
    public function getSearchPageSize($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::SEARCH_PAGE_SIZE, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return int
     */
    public function getFaqPageShortAnswerBehavior($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::FAQ_PAGE_SHORT_ANSWER_BEHAVIOR, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return int
     */
    public function getProductPageShortAnswerBehavior($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->getValue(self::PRODUCT_PAGE_SHORT_ANSWER_BEHAVIOR, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return string
     */
    public function getPageLayout($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::PAGE_LAYOUT, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return bool
     */
    public function isUseFaqCmsHomePage($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::USE_FAQ_CMS_HOME_PAGE, $scopeType);
    }

    /**
     * @param string $scopeType
     * @return string
     */
    public function getFaqCmsHomePage($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::FAQ_CMS_HOME_PAGE, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return bool
     */
    public function isGDPREnabled($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->getValue(self::GDPR_ENABLED, $scopeType);
    }

    /**
     * @param string $scopeType
     *
     * @return string
     */
    public function getGDPRText($scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue(self::GDPR_TEXT, $scopeType);
    }
}
