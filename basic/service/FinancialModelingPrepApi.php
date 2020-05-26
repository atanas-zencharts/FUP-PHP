<?php

namespace app\service;

use app\components\CompanyHelper;
use app\models\Company;
use app\models\CompanyPriceHistory;
use app\models\Cryptocurency;
use app\models\CryptoPriceHistory;
use app\models\Forex;
use app\models\ForexHistory;
use app\models\MajorIndex;
use app\models\MajorIndexPriceHistory;
use Yii;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\helpers\VarDumper;

/**
 *
 *
 * Class FinancialModelingPrepApi
 */
class FinancialModelingPrepApi
{
    /** The API key used to verify the user */
    const API_KEY = '?apikey=20f7a0cb3aa7e6eb72f541597481fe16';

    /** The base url to the financial modeling prep API */
    const BASE_URL = 'https://financialmodelingprep.com';

    /**
     * Company Profile
     * Example url https://financialmodelingprep.com/api/v3/profile/AAPL?apikey=demo
     */
    const PROFILE = '/api/v3/profile/';

    /**
     * Symbols List. All Companies ticker symbols available in Financial Modeling Prep.
     * Example url https://financialmodelingprep.com/api/v3/stock/list?apikey=demo
     */
    const LIST = '/api/v3/stock/list';

    /**
     * Batch Request Stock Companies Price. Multiple companies Prices
     * Example url https://financialmodelingprep.com/api/v3/quote/AAPL,FB,GOOG?apikey=demo
     */
    const QUOTE = '/api/v3/quote/';

    /**
     * All Majors Indexes
     * Example url https://financialmodelingprep.com/api/v3/quotes/index?apikey=demo
     */
    const MAJOR_INDEXES = '/api/v3/quotes/index';

    /**
     * Currency exchange rate such as Euro-dollars (EUR/USD)
     * Example url https://financialmodelingprep.com/api/v3/fx/EURUSD?apikey=demo
     */
    const FOREX = '/api/v3/fx/';

    /** @var array List with crypto currency symbols. */
    const LIST_CRYPTO = [
        1 => 'BTCUSD',
        2 => 'ETHUSD',
        3 => 'USDTUSD',
        4 => 'XRPUSD',
        5 => 'BCHUSD',
        6 => 'ADAUSD',
        7 => 'LTCUSD',
        8 => 'BNBUSD',
        9 => 'EOSUSD',
        10 => 'LINKUSD'
    ];

    /** @var array List with major index symbols */
    const LIST_MAJOR_INDEX = [
        1 => '%5EN225',
        2 => '%5EGSPC',
        3 => '%5EGDAXI',
        4 => '%5EIXIC',
        5 => '%5ENDX',
        6 => '%5ENYA',
        7 => '%5EN100',
        8 => '%5EFCHI',
        9 => '%5ERMCCTR',
        10 => '%5ESSEC',
    ];

    /** @var array List with currency pairs tickers */
    const LIST_FOREX = [
        1 => 'EURUSD',
        2 => 'USDJPY',
        3 => 'GBPUSD',
        4 => 'EURGBP',
        5 => 'USDCHF',
        6 => 'EURJPY',
        7 => 'EURCHF',
        8 => 'GBPJPY',
        9 => 'USDCAD',
        10 => 'AUDUSD'
    ];

    const LIST_COMPANIES = [
        1 => 'AAPL',
        2 => 'AMD',
        3 => 'MSFT',
        4 => 'BABA',
        5 => 'EA',
        6 => 'KHC',
        7 => 'EBAY',
        8 => 'DELL',
        9 => 'TSLA',
        10 => 'SNE',
        11 => 'BA',
        12 => 'BRK-B',
        13 => 'BKNG',
        14 => 'CAT',
        15 => 'CWEN',
        16 => 'CY',
        17 => 'DISCA',
        18 => 'ADS',
        19 => 'DSX',
        20 => 'FB',
        21 => 'XOM',
        22 => 'UHT',
        23 => 'NBHC',
        24 => 'NFLX',
        25 => 'ACER',
        26 => 'NVDA',
        27 => 'EPAY',
        28 => 'UMC',
        29 => 'HMC',
        30 => 'MCD',
    ];

    public function getCrypto()
    {
        foreach (self::LIST_CRYPTO AS $crypto) {
            $url = self::BASE_URL . self::QUOTE . $crypto . self::API_KEY;
            $response = file_get_contents($url);
            $data = Json::decode($response);

            $cryptocurency = Cryptocurency::find()->andWhere(['symbol' => $crypto])->one();
            if (!$cryptocurency) {
                $cryptocurency = new Cryptocurency();
                $cryptocurency->name = $data[0]['name'];
                $cryptocurency->symbol = $data[0]['symbol'];
            }

            $cryptocurency->price = $data[0]['price'];
            $cryptocurency->change = $data[0]['change'];
            $cryptocurency->capitalization = $data[0]['marketCap'];
            $cryptocurency->dayHigh = $data[0]['dayHigh'];
            $cryptocurency->dayLow = $data[0]['dayLow'];
            $cryptocurency->open = $data[0]['open'];
            $cryptocurency->previousDay = $data[0]['previousClose'];

            if (!$cryptocurency->save()) {
                Yii::error(VarDumper::dumpAsString([
                     $cryptocurency->getErrors()
                 ]));
            } else {
                $history = new CryptoPriceHistory();
                $history->crypto_id = $cryptocurency->id;
                $history->price = $cryptocurency->price;
                $history->capitalization = $cryptocurency->capitalization;
                $history->date = (new \DateTime())->format(DATE_W3C);

                if (!$history->save()) {
                    Yii::error(VarDumper::dumpAsString([
                         $history->getErrors()
                     ]));
                }
            }

        }
    }

    public function getMajorIndex()
    {
        foreach (self::LIST_MAJOR_INDEX AS $KEY =>  $INDEX) {
            $url = self::BASE_URL . self::QUOTE . $INDEX . self::API_KEY;
            $response = file_get_contents($url);
            $data = Json::decode($response);

            $majorIndex = MajorIndex::find()->andWhere(['id' => $KEY])->one();
            if (!$majorIndex) {
                $majorIndex = new MajorIndex();
                $majorIndex->name = $data[0]['name'];
                $majorIndex->symbol = $data[0]['symbol'];
            }

            $majorIndex->price = $data[0]['price'];
            $majorIndex->change = $data[0]['change'];
            $majorIndex->changePercent = $data[0]['changesPercentage'];
            $majorIndex->dayHigh = $data[0]['dayHigh'];
            $majorIndex->dayLow = $data[0]['dayLow'];
            $majorIndex->open = $data[0]['open'];
            $majorIndex->previousDay = $data[0]['previousClose'];

            if (!$majorIndex->save()) {
                Yii::error(VarDumper::dumpAsString([
                    $majorIndex->getErrors()
                ]));
            } else {
                $history = new MajorIndexPriceHistory();
                $history->major_index_id = $majorIndex->id;
                $history->price = $majorIndex->price;
                $history->date = (new \DateTime())->format(DATE_W3C);

                if (!$history->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $history->getErrors()
                    ]));
                }
            }
        }
    }

    public function getForex()
    {
        foreach (self::LIST_FOREX AS $KEY => $FX) {
            $url = self::BASE_URL . self::FOREX . $FX . self::API_KEY;
            $response = file_get_contents($url);
            $data = Json::decode($response);

            $forex = Forex::find()->andWhere(['id' => $KEY])->one();
            if (!$forex) {
                $forex = new Forex();
                $forex->ticker = $data[0]['ticker'];
            }
            $forex->bid = $data[0]['bid'];
            $forex->ask = $data[0]['ask'];
            $forex->open = $data[0]['open'];
            $forex->low = $data[0]['low'];
            $forex->high = $data[0]['high'];
            $forex->changes = $data[0]['changes'];
            $forex->date = $data[0]['date'];

            if (!$forex->save()) {
                Yii::error(VarDumper::dumpAsString([
                    $forex->getErrors()
                ]));
            } else {
                $history = new ForexHistory();
                $history->bid = $forex->bid;
                $history->ask = $forex->ask;
                $history->open = $forex->open;
                $history->low = $forex->low;
                $history->high = $forex->high;
                $history->date = (new \DateTime())->format(DATE_W3C);

                if (!$history->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $history->getErrors()
                    ]));
                }
            }
        }
    }

    public function getCompany()
    {
        foreach (self::LIST_COMPANIES AS $Key => $companySymbol) {
            $urlProfile = self::BASE_URL . self::PROFILE . $companySymbol . self::API_KEY;
            $urlQuote = self::BASE_URL . self::QUOTE . $companySymbol . self::API_KEY;
            $responseProfile = file_get_contents($urlProfile);
            $dataProfile = Json::decode($responseProfile);
            $responseQuote = file_get_contents($urlQuote);
            $dataQuote = Json::decode($responseQuote);

            Yii::error(VarDumper::dumpAsString([
                 'DP' => $dataProfile,
                 'DQ' => $dataQuote
             ]));

            $company = Company::find()->andWhere(['symbol' => $companySymbol])->one();
            if (!$company) {
                $company = new Company();
            }
            $company->symbol = $dataProfile[0]['symbol'];
            $company->name = $dataProfile[0]['companyName'];
            $company->beta = $dataProfile[0]['beta'];
            $company->volAvg = $dataProfile[0]['volAvg'];
            $company->mktCap = $dataProfile[0]['mktCap'];
            $company->lastDiv = $dataProfile[0]['lastDiv'];
            $company->range = $dataProfile[0]['range'];
            $company->website = $dataProfile[0]['website'];
            $company->description = $dataProfile[0]['description'];
            $company->ceo = $dataProfile[0]['ceo'];
            $company->image = $dataProfile[0]['image'];
            $company->sector_id = CompanyHelper::getSectorIdByName($dataProfile[0]['sector']);
            $company->exchage_id = CompanyHelper::getExchangeIdByName($dataProfile[0]['exchange']);
            $company->industry_id = CompanyHelper::getIndustryIdByName($dataProfile[0]['industry']);
            $company->price = $dataQuote[0]['price'];
            $company->changes = $dataQuote[0]['change'];
            $company->changePercentage = $dataQuote[0]['changesPercentage'];
            $company->dayHigh = $dataQuote[0]['dayHigh'];
            $company->dayLow = $dataQuote[0]['dayLow'];
            $company->open = $dataQuote[0]['open'];
            $company->previousDay = $dataQuote[0]['previousClose'];

            if (!$company->save()) {
                Yii::error(VarDumper::dumpAsString([
                     $company->getErrors()
                 ]));
            } else {
                $history = new CompanyPriceHistory();
                $history->date = (new \DateTime())->format(DATE_W3C);
                $history->price = $dataQuote[0]['price'];
                $history->dayHigh = $dataQuote[0]['dayHigh'];
                $history->dayLow = $dataQuote[0]['dayLow'];
                $history->open = $dataQuote[0]['open'];
                $history->previousDay = $dataQuote[0]['previousClose'];
                $history->company_id = $company->id;

                if (!$history->save()) {
                    Yii::error(VarDumper::dumpAsString([
                         $history->getErrors()
                     ]));
                }
            }
        }
    }
}