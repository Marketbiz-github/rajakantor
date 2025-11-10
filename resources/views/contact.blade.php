@extends('layouts.app-landingpage')

@section('content')

{{-- CENTER CONTENT --}}
<main class="order-0 md:order-none">
  <div class="bg-white p-4 text-[13px] text-[#444]">
    <div class="max-w-2xl mx-auto">
      <div class="mb-6">
        <h2 class="text-lg font-bold text-[#333] mb-2">Contact Us</h2>
        <p class="text-gray-600">Send us a message and we'll get back to you as soon as possible.</p>
      </div>

      <form id="contactForm" class="space-y-6 bg-gray-50 p-6 rounded border border-gray-300">
        <!-- Name -->
        <div>
          <label for="name" class="block text-xs font-semibold text-[#333] mb-1">Name *</label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            placeholder="Your name" 
            required
            class="w-full p-2 border text-xs border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#cf6a00]"
          />
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-xs font-semibold text-[#333] mb-1">Email *</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="your@email.com" 
            required
            class="w-full p-2 text-xs border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#cf6a00]"
          />
        </div>

        <!-- Phone -->
        <div>
          <label for="phone" class="block text-xs font-semibold text-[#333] mb-1">Phone *</label>
          <input 
            type="tel" 
            id="phone" 
            name="phone" 
            placeholder="Your phone number" 
            required
            class="w-full p-2 text-xs border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#cf6a00]"
          />
        </div>

        <!-- Subject -->
        <div>
          <label for="subject" class="block text-xs font-semibold text-[#333] mb-1">Subject *</label>
          <input 
            type="text" 
            id="subject" 
            name="subject" 
            placeholder="What is this about?" 
            required
            class="w-full p-2 text-xs border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#cf6a00]"
          />
        </div>

        <!-- Message -->
        <div>
          <label for="message" class="block text-xs font-semibold text-[#333] mb-1">Message *</label>
          <textarea 
            id="message" 
            name="message" 
            placeholder="Your message here..." 
            required
            rows="5"
            class="w-full p-2 text-xs border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#cf6a00]"
          ></textarea>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition flex items-center justify-center gap-2"
        >
          <i class="fa-brands fa-whatsapp"></i> Send via WhatsApp
        </button>
      </form>

    </div>
  </div>
</main>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const name = document.getElementById('name').value.trim();
  const email = document.getElementById('email').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const subject = document.getElementById('subject').value.trim();
  const message = document.getElementById('message').value.trim();

  if (!name || !email || !phone || !subject || !message) {
    alert('Please fill in all fields');
    return;
  }

  // Build WhatsApp message
  const waMessage = `*Contact Form Submission*\n\n` +
    `*Name:* ${name}\n` +
    `*Email:* ${email}\n` +
    `*Phone:* ${phone}\n` +
    `*Subject:* ${subject}\n` +
    `*Message:* ${message}`;

  // WhatsApp number (from site settings, remove any non-digit characters)
  const waNumber = "{{ $siteSettings->wa ?? '' }}".replace(/\D/g, '');

  if (!waNumber) {
    alert('WhatsApp number not configured. Please contact us directly.');
    return;
  }

  // Build WhatsApp URL
  const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`;

  // Open WhatsApp
  window.open(waUrl, '_blank');

  // Reset form
  this.reset();
});
</script>

@endsection

