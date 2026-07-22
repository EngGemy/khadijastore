{{-- حالة فارغة لدليل الأطباء / الحضانات --}}
@php $isDoctor = ($type ?? '') === 'doctor'; @endphp
<div class="text-center py-16 px-6 rounded-[24px] border border-dashed border-line bg-paper">
  <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-ink/5 grid place-items-center text-ink/30">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
  </div>
  <p class="text-[16px] font-extrabold text-ink mb-1">لا توجد نتائج</p>
  <p class="text-[13px] text-ink/45 font-medium max-w-sm mx-auto mb-5">
    {{ $isDoctor
        ? 'لم نجد أطباء بهذه الفلاتر. جرّب تغيير التخصص أو المحافظة.'
        : 'لم نجد حضانات بهذه الفلاتر. جرّب تغيير المنطقة أو كلمات البحث.' }}
  </p>
  <a href="{{ route('directory.index', $type) }}"
     class="inline-flex items-center gap-2 bg-ink text-paper font-bold rounded-xl px-5 py-2.5 text-[13px] hover:bg-ink2 transition">
    عرض الكل
  </a>
</div>
