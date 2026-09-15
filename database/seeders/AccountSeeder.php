<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ======================
            // 1xxx - الأصول (Assets) - طبيعتها مدين
            // ======================
            [
                'code'           => '1001',
                'name'           => 'الصندوق',
                'name_en'        => 'Cash',
                'type'           => Account::TYPE_ASSET,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'النقدية في الصندوق',
                'is_system'      => true,
            ],
            [
                'code'           => '1002',
                'name'           => 'البنك',
                'name_en'        => 'Bank',
                'type'           => Account::TYPE_ASSET,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'النقدية في الحساب البنكي',
                'is_system'      => true,
            ],
            [
                'code'           => '1003',
                'name'           => 'المخزون',
                'name_en'        => 'Inventory',
                'type'           => Account::TYPE_ASSET,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'قيمة البضاعة في المستودع',
                'is_system'      => true,
            ],
            [
                'code'           => '1004',
                'name'           => 'الذمم المدينة',
                'name_en'        => 'Accounts Receivable',
                'type'           => Account::TYPE_ASSET,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'المبالغ المستحقة من العملاء',
                'is_system'      => true,
            ],

            // ======================
            // 2xxx - الخصوم (Liabilities) - طبيعتها دائن
            // ======================
            [
                'code'           => '2001',
                'name'           => 'الذمم الدائنة',
                'name_en'        => 'Accounts Payable',
                'type'           => Account::TYPE_LIABILITY,
                'normal_balance' => Account::BALANCE_CREDIT,
                'description'    => 'المبالغ المستحقة للموردين',
                'is_system'      => true,
            ],

            // ======================
            // 3xxx - حقوق الملكية (Equity) - طبيعتها دائن
            // ======================
            [
                'code'           => '3001',
                'name'           => 'رأس المال',
                'name_en'        => 'Capital',
                'type'           => Account::TYPE_EQUITY,
                'normal_balance' => Account::BALANCE_CREDIT,
                'description'    => 'رأس مال المنصة',
                'is_system'      => true,
            ],

            // ======================
            // 4xxx - الإيرادات (Revenue) - طبيعتها دائن
            // ======================
            [
                'code'           => '4001',
                'name'           => 'إيرادات المبيعات',
                'name_en'        => 'Sales Revenue',
                'type'           => Account::TYPE_REVENUE,
                'normal_balance' => Account::BALANCE_CREDIT,
                'description'    => 'إيرادات بيع المنتجات',
                'is_system'      => true,
            ],

            // ======================
            // 5xxx - النفقات (Expenses) - طبيعتها مدين
            // ======================
            [
                'code'           => '5001',
                'name'           => 'تكلفة البضاعة المباعة',
                'name_en'        => 'Cost of Goods Sold',
                'type'           => Account::TYPE_EXPENSE,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'تكلفة المنتجات التي تم بيعها',
                'is_system'      => true,
            ],
            [
                'code'           => '5002',
                'name'           => 'مصاريف تشغيلية',
                'name_en'        => 'Operating Expenses',
                'type'           => Account::TYPE_EXPENSE,
                'normal_balance' => Account::BALANCE_DEBIT,
                'description'    => 'المصاريف العامة للمنصة',
                'is_system'      => false,
            ],
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(
                ['code' => $account['code']],
                $account
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($accounts) . ' حساب محاسبي بنجاح!');
    }
}