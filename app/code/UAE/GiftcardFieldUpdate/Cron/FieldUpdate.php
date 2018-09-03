<?php
/**
 * Cron update fields
 *
 * @category UAE
 * @package UAE_GiftcardFieldUpdate
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\GiftcardFieldUpdate\Cron;

use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Xml\Parser;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * UAE\GiftcardFieldUpdate\Cron\FieldUpdate
 *
 * @category UAE
 * @package UAE_GiftcardFieldUpdate
 */
class FieldUpdate
{
    /**#@+
     * @const FTP settings
     */
    const FTP_HOST_PATH = 'aw_giftcard_field_update/general/host';
    const FTP_USERNAME_PATH = 'aw_giftcard_field_update/general/username';
    const FTP_PASSWORD_PATH = 'aw_giftcard_field_update/general/password';
    /**#@-*/

    /**
     * @var Ftp
     */
    private $ftpAdapter;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FieldUpdate constructor.
     *
     * @param Ftp $ftpAdapter
     * @param Parser $parser
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     */
    public function __construct(
        Ftp $ftpAdapter,
        Parser $parser,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resource,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->ftpAdapter = $ftpAdapter;
        $this->parser = $parser;
        $this->scopeConfig = $scopeConfig;
        $this->filesystem = $filesystem;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * Execute
     */
    public function execute()
    {
        try {
            $this->ftpAdapter->open($this->getFtpSettings());
            $this->ftpAdapter->cd('GIFTCARDS');
            $tmpDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $result = $this->ftpAdapter->read('giftCards.xml', $tmpDirectory->getAbsolutePath('tmp'));
            $data = $this->parser->load($tmpDirectory->getAbsolutePath('tmp') . '/test.xml')
                ->xmlToArray();


            $connection = $this->resource->getConnection();
            $id = '';
            $code = '';
            $state = '';
            $balance = '';

            $connection->update(
                $this->resource->getTableName('aw_giftcard'),
                ['id' => $id],
                [
                    'code = ?' => $code,
                    'state = ?' => $state,
                    'balance = ?' => $balance
                ]
            );

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Retrieve Ftp config
     *
     * @return array
     */
    private function getFtpSettings()
    {
        return [
            'host' => $this->scopeConfig->getValue(
                self::FTP_HOST_PATH,
                ScopeInterface::SCOPE_STORE
            ),
            'user' => $this->scopeConfig->getValue(
                self::FTP_USERNAME_PATH,
                ScopeInterface::SCOPE_STORE
            ),
            'password' => $this->scopeConfig->getValue(
                self::FTP_PASSWORD_PATH,
                ScopeInterface::SCOPE_STORE
            ),
        ];
    }
}
