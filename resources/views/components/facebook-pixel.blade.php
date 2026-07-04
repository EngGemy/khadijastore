@if($settings)
@push('head')
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
window.__fbPixelConsentRequired = @json($requireConsent);
window.__fbPixelConsentCookie = @json($consentCookie);
window.__fbPixelConsentValue = @json($consentValue);
window.__fbPixelHasConsent = function () {
  if (!window.__fbPixelConsentRequired) return true;
  var match = document.cookie.match(new RegExp('(?:^|; )' + window.__fbPixelConsentCookie.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
  return match && decodeURIComponent(match[1]) === window.__fbPixelConsentValue;
};
window.__fbPixelTrack = function (eventId, eventName, params) {
  if (typeof fbq !== 'function' || !window.__fbPixelHasConsent()) return;
  fbq('track', eventName, params || {}, { eventID: eventId });
};
fbq('init', @json($settings->pixel_id));
@if($settings->track_pageview && $pageViewEventId)
fbq('track', 'PageView', {}, { eventID: @json($pageViewEventId) });
@endif
</script>
<noscript>
  <img height="1" width="1" style="display:none"
       src="https://www.facebook.com/tr?id={{ $settings->pixel_id }}&ev=PageView&noscript=1" alt="">
</noscript>
@endpush

@push('scripts')
@foreach($browserEvents as $event)
<script>
window.__fbPixelTrack && window.__fbPixelTrack(
  @json($event['event_id']),
  @json($event['event_name']),
  @json($event['params'])
);
</script>
@endforeach
@stack('fb-pixel-events')
@endpush
@endif
