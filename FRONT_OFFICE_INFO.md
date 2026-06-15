The "Front Office" / Customer-Facing Website
The application has a public-facing website implemented as standalone Livewire page components (using anonymous/one-off Livewire classes). they use Livewire\Component directly with a custom layouts::app layout.

Where it lives:
Livewire component PHP classes: resources/views/pages/facing/**/*.php (anonymous new class extends Component { ... })
Blade templates: resources/views/pages/facing/**/*.blade.php
Routes: routes/web.php (uses Route::livewire() with the pages::facing. namespace)
Layout: resources/views/layouts/app.blade.php (uses Flux UI components)
Reusable components: resources/views/components/home/* (hero, packages-highlight, why-us, how-it-works, testimonials, news-section, whatsapp-button)
Footer: resources/views/components/footer/ (shared Livewire component)
Front Office / Facing Routes & Pages:
Route	Name	Title (Arabic)	Description
/	home	رحلات الحج والعمرة	Home page with hero, featured packages, why-us, how-it-works, testimonials, news, WhatsApp button
/packages	packages.index	الباقات	All packages with filtering (type, grade, year, price range, duration)
/packages/vip	packages.vip	باقات VIP	VIP/VVIP packages only
/packages/featured	packages.featured	العروض المميزة	Current year featured packages
/packages/groups	packages.groups	رحلات المجموعات	Group packages (10+ seats available)
/packages/{package:slug}	packages.show	تفاصيل الباقة	Single package detail with pricing (single/double/triple/quad)
/book/{package:slug}	book	احجز الآن	Booking form for a package (creates Client + Booking records)
/track	track	تتبع الحجز	Track booking by reference + national ID
/about	about	من نحن	About us page
/contact	contact	اتصل بنا	Contact form
/news	news.index	الأخبار والإعلانات	Paginated news list
/news/{post:slug}	news.show	تفاصيل الخبر	Single news article
/faq	faq	الأسئلة الشائعة	FAQ page
/gallery	gallery	معرض الصور	Gallery page
/privacy	privacy	سياسة الخصوصية	Privacy policy
/terms	terms	الشروط والأحكام	Terms & conditions
/cancellation	cancellation	سياسة الإلغاء والاسترداد	Cancellation policy
The front_office_visible Link
The Package model has a front_office_visible boolean field (default true), exposed as a Toggle in PackageForm.php with the label "ظاهرة للمكتب الأمامي" (Visible to the front office). This controls whether a package appears on the public-facing site. In the packages() query on the packages listing page, it filters ->where('is_active', true) but does NOT explicitly filter by front_office_visible -- however, the field exists as a visibility control mechanism for future/per-page use.