<footer class="bg-ink text-paper relative overflow-hidden" style="padding:64px 0 28px">
  <div class="absolute -top-1/2 -start-[10%] w-[400px] h-[400px] pointer-events-none" style="background:radial-gradient(circle,rgba(232,93,4,.12),transparent 70%)"></div>
  <div class="max-w-[1180px] mx-auto px-5 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-[2.2fr_1fr_1fr_1.2fr] gap-9">
      <div>
        <div class="flex items-center gap-2.5 font-extrabold text-lg mb-3.5">
          <span class="w-10 h-10 rounded-xl bg-white text-ink grid place-items-center font-extrabold text-lg">ع</span>
          {{ $storeName ?? 'متجر العلامات' }}
        </div>
        <p class="text-white/50 text-sm max-w-[300px] leading-relaxed">وجهتك الموثوقة لأفضل البراندات. دفع عند الاستلام وتوصيل لكل المحافظات.</p>
      </div>
      <div>
        <h4 class="font-bold text-sm mb-3.5">المتجر</h4>
        <a href="{{ route('products.index') }}" class="block text-white/55 text-sm leading-loose hover:text-white transition">المنتجات</a>
        <a href="{{ route('brands.index') }}" class="block text-white/55 text-sm leading-loose hover:text-white transition">البراندات</a>
        <a href="{{ route('directory.index','doctor') }}" class="block text-brand text-sm leading-loose hover:opacity-80 transition">دليل الأطباء</a>
        <a href="{{ route('directory.index','nursery') }}" class="block text-brand text-sm leading-loose hover:opacity-80 transition">دليل الحضانات</a>
      </div>
      <div>
        <h4 class="font-bold text-sm mb-3.5">السياسات</h4>
        <a href="#" class="block text-white/55 text-sm leading-loose hover:text-white transition">الاستبدال والاسترجاع</a>
        <a href="#" class="block text-white/55 text-sm leading-loose hover:text-white transition">الخصوصية</a>
        <a href="#" class="block text-white/55 text-sm leading-loose hover:text-white transition">الشروط</a>
      </div>
      <div>
        <h4 class="font-bold text-sm mb-3.5">تواصل · Contact</h4>
        @if(!empty($storeSupportPhone))
        <a href="tel:{{ preg_replace('/\D/', '', $storeSupportPhone) }}" class="block text-white/55 text-sm leading-loose hover:text-white transition en">{{ $storeSupportPhone }}</a>
        @endif
        @if(!empty($storeSupportWhatsapp))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $storeSupportWhatsapp) }}" class="block text-white/55 text-sm leading-loose hover:text-white transition">واتساب</a>
        @endif
      </div>
    </div>
    <div class="border-t border-white/10 mt-10 text-[13px] text-white/40 text-center" style="padding-top:22px">© {{ date('Y') }} {{ $storeName ?? 'متجر العلامات' }} · جميع الحقوق محفوظة · <span class="en">All rights reserved</span></div>
  </div>
</footer>
