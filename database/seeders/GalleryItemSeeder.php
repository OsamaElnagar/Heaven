<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        $company = config('app.name');

        $items = [
            ['title' => 'الحرم المكي الشريف', 'caption' => 'إطلالة ساحرة على الحرم المكي من فندق أبراج البيت', 'sort_order' => 1],
            ['title' => 'باب الملك عبدالعزيز', 'caption' => 'باب الملك عبدالعزيز - أحد أبواب الحرم المكي المزدوجة', 'sort_order' => 2],
            ['title' => 'المطافئ المكي', 'caption' => 'مشاهد من داخل المطافئduring أداء مناسك العمرة', 'sort_order' => 3],
            ['title' => 'الحرم النبوي الشريف', 'caption' => 'المسجد النبوي في المدينة المنورة - رابعة أقدس مكان في الإسلام', 'sort_order' => 4],
            ['title' => 'قبة المسجد النبوي', 'caption' => 'القبة الخضراء في المسجد النبوي الشريف', 'sort_order' => 5],
            ['title' => 'جبل النور', 'caption' => 'جبل النور في مكة المكرمة - 위치 غار حراء', 'sort_order' => 6],
            ['title' => 'سوق عكاظ', 'caption' => 'تجول في سوق عكاظ التراثي القريب من الحرم', 'sort_order' => 7],
            ['title' => 'متحف الحج', 'caption' => 'متحف الحج والعمرة - يعرض تاريخ الحج عبر العصور', 'sort_order' => 8],
            ['title' => 'فندق موفمبيك مكة', 'caption' => 'فندق موفمبيك مكة - إطلالة رائعة على الحرم', 'sort_order' => 9],
            ['title' => 'مجمع أبراج البيت', 'caption' => 'مجمع أبراج البيت - من أفخم الفنادق المطلة على الحرم', 'sort_order' => 10],
            ['title' => 'المسجد الحرام ليلاً', 'caption' => 'منظر خلاب للمسجد الحرم في الليل مع الإضاءة المميزة', 'sort_order' => 11],
            ['title' => 'الصفوف المرتدة', 'caption' => 'أثناء أداء صلاة الفجر في الحرم المكي الشريف', 'sort_order' => 12],
            ['title' => 'زقاق التاريخ في مكة', 'caption' => 'أزقة مكة التاريخية القديمة', 'sort_order' => 13],
            ['title' => 'البواكي في المشاعر المقدسة', 'caption' => 'بواكي الحجاج في منىDuring موسم الحج', 'sort_order' => 14],
            ['title' => "مكتب شركة {$company}", 'caption' => "المكتب الرئيسي لشركة {$company} للسياحة بالقاهرة", 'sort_order' => 15],
        ];

        foreach ($items as $data) {
            GalleryItem::create([
                'title' => $data['title'],
                'caption' => $data['caption'],
                'sort_order' => $data['sort_order'],
                'is_published' => true,
            ]);
        }
    }
}
