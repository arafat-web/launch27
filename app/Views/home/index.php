<nav id="navbar">
    <a href="<?= View::url('/') ?>" class="logo"><span class="logo-desktop">Bronx<span>HomeServices</span></span><span class="logo-mobile">B<span>HS</span></span></a>
    
    <div class="mobile-toggle" id="mobileToggle">
        <i class="fa-solid fa-bars"></i>
    </div>

    <ul class="nav-links" id="navLinks">
        <li><a href="<?= View::url('/') ?>#services">Services</a></li>
        <li><a href="<?= View::url('/') ?>#how">Process</a></li>
        <li><a href="<?= View::url('/') ?>#why">Why Us</a></li>
        <li><a href="<?= View::url('/') ?>#reviews">Reviews</a></li>
        <li><a href="<?= View::url('booking') ?>" class="btn-nav">Book Now <i class="fa-solid fa-arrow-right"></i></a>
        </li>
    </ul>
</nav>

<!-- ── HERO ── -->
<section class="hero" id="home">
    <div class="hero-grid-overlay"></div>
    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1400&q=70"
        alt="Clean professional interior" class="hero-img">
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fa-solid fa-shield-halved"></i>
            Verified &amp; Insured Professionals
        </div>
        <h1>Professional Cleaning <span>Services</span> You Can Trust</h1>
        <p>Background-checked, insured cleaners delivering consistent results. Book online in 60 seconds — no phone
            calls required.</p>
        <div class="hero-actions">
            <a href="<?= View::url('booking') ?>" class="btn-primary">
                <i class="fa-solid fa-calendar-check"></i> Book a Cleaning
            </a>
            <a href="#services" class="btn-secondary">
                <i class="fa-solid fa-chevron-down"></i> View Services
            </a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat"><span class="num">5,000<span>+</span></span>
                <p>Clients served</p>
            </div>
            <div class="hero-stat"><span class="num">4.9<span>★</span></span>
                <p>Average rating</p>
            </div>
            <div class="hero-stat"><span class="num">100<span>%</span></span>
                <p>Satisfaction guarantee</p>
            </div>
            <div class="hero-stat"><span class="num">60<span>s</span></span>
                <p>To book online</p>
            </div>
        </div>
    </div>
</section>

<!-- ── TRUST BAR ── -->
<div class="trust-bar">
    <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> Fully Insured &amp; Bonded</div>
    <div class="trust-item"><i class="fa-solid fa-user-check"></i> Background Checked</div>
    <div class="trust-item"><i class="fa-solid fa-leaf"></i> Eco-Friendly Products</div>
    <div class="trust-item"><i class="fa-solid fa-lock"></i> Secure Online Booking</div>
    <div class="trust-item"><i class="fa-solid fa-rotate-left"></i> Free Rescheduling</div>
</div>

<!-- ── SERVICES ── -->
<section class="services-section" id="services">
    <div class="text-center reveal">
        <div class="section-label" style="justify-content:center;">Services</div>
        <h2 class="section-title">A Cleaning Plan for Every Home</h2>
        <p class="section-sub center">From routine maintenance to full deep cleans — we have the right service at the
            right price.</p>
    </div>
    <div class="services-grid reveal">
        <div class="svc-card">
            <div class="svc-icon"><i class="fa-solid fa-broom"></i></div>
            <div class="svc-name">Standard Cleaning</div>
            <p class="svc-desc">Regular upkeep covering kitchens, bathrooms, bedrooms, and living areas. Perfect for
                weekly routines.</p>
            <div class="svc-price">From $99 <span>/ visit</span></div>
        </div>
        <div class="svc-card">
            <div class="svc-icon"><i class="fa-solid fa-sparkles"></i></div>
            <div class="svc-name">Deep Cleaning</div>
            <p class="svc-desc">Thorough top-to-bottom clean including appliances, baseboards, vents, and all
                hard-to-reach areas.</p>
            <div class="svc-price">From $199 <span>/ visit</span></div>
        </div>
        <div class="svc-card">
            <div class="svc-icon"><i class="fa-solid fa-box-open"></i></div>
            <div class="svc-name">Move In / Move Out</div>
            <p class="svc-desc">Leave your old place immaculate or start fresh in a new one. Ideal for landlords and
                tenants.</p>
            <div class="svc-price">From $249 <span>/ visit</span></div>
        </div>
        <div class="svc-card">
            <div class="svc-icon"><i class="fa-solid fa-building"></i></div>
            <div class="svc-name">Office Cleaning</div>
            <p class="svc-desc">Keep your workspace healthy and professional. Scheduled around your working hours —
                always flexible.</p>
            <div class="svc-price">From $149 <span>/ visit</span></div>
        </div>
    </div>
    <div class="services-cta reveal">
        <a href="<?= View::url('booking') ?>" class="btn-primary">
            <i class="fa-solid fa-arrow-right"></i> Book Any Service Online
        </a>
    </div>
</section>

<!-- ── HOW IT WORKS ── -->
<section class="how-section" id="how">
    <div class="text-center reveal">
        <div class="section-label" style="justify-content:center;color:#60A5FA;">
            <span style="width:20px;height:2px;background:#60A5FA;display:inline-block;"></span>
            How It Works
        </div>
        <h2 class="section-title">Simple. Fast. Reliable.</h2>
        <p class="section-sub center" style="color:#64748B;">No calls, no back-and-forth. Book in four easy steps and
            relax.</p>
    </div>
    <div class="steps-grid reveal">
        <div class="step-col">
            <div class="step-num">Step 01</div>
            <div class="step-icon"><i class="fa-solid fa-house"></i></div>
            <h3>Choose Your Service</h3>
            <p>Pick the cleaning package that matches your home size and requirements.</p>
        </div>
        <div class="step-col">
            <div class="step-num">Step 02</div>
            <div class="step-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <h3>Pick a Date &amp; Time</h3>
            <p>Browse real-time available slots and choose what works best for your schedule.</p>
        </div>
        <div class="step-col">
            <div class="step-num">Step 03</div>
            <div class="step-icon"><i class="fa-solid fa-file-lines"></i></div>
            <h3>Provide Your Details</h3>
            <p>Enter your address and any special instructions. The whole process takes under a minute.</p>
        </div>
        <div class="step-col">
            <div class="step-num">Step 04</div>
            <div class="step-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h3>Sit Back &amp; Relax</h3>
            <p>Our vetted professionals arrive on time and deliver a spotless result — guaranteed.</p>
        </div>
    </div>
</section>

<!-- ── WHY US ── -->
<section class="why-section" id="why">
    <div class="why-layout">
        <div class="reveal">
            <div class="section-label">Why Choose BronxHomeServices</div>
            <h2 class="section-title">Built on Trust,<br>Delivered with Precision</h2>
            <p class="section-sub">We don't just clean homes — we build long-term relationships with clients who expect
                the best.</p>
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div>
                        <h4>Fully Insured &amp; Bonded</h4>
                        <p>Every cleaner is fully insured, so you're always protected — regardless of what happens.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div>
                        <h4>Rigorous Background Checks</h4>
                        <p>Multi-step screening ensures only the most trusted professionals enter your home.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-leaf"></i></div>
                    <div>
                        <h4>Eco-Certified Products</h4>
                        <p>We use non-toxic, green-certified cleaning solutions — safe for children and pets.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-rotate"></i></div>
                    <div>
                        <h4>100% Satisfaction Guarantee</h4>
                        <p>Not entirely happy? We'll return and re-clean at absolutely no extra charge.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="reveal">
            <div class="why-visual">
                <div class="why-visual-title">Our Service Standards</div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-star"></i> Customer
                        Rating</span>
                    <div style="display:flex;align-items:center;gap:10px;"><span class="metric-val">4.9 / 5</span><span
                            class="metric-badge">Excellent</span></div>
                </div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-clock"></i> On-Time
                        Arrivals</span>
                    <div style="display:flex;align-items:center;gap:10px;"><span class="metric-val">98%</span><span
                            class="metric-badge">↑ 2%</span></div>
                </div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-users"></i> Active
                        Clients</span><span class="metric-val">5,200+</span></div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-thumbs-up"></i> Re-booking
                        Rate</span>
                    <div style="display:flex;align-items:center;gap:10px;"><span class="metric-val">87%</span><span
                            class="metric-badge">High</span></div>
                </div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-calendar-check"></i> Cleanings
                        Completed</span><span class="metric-val">40,000+</span></div>
                <div class="metric-row"><span class="metric-label"><i class="fa-solid fa-award"></i> Years in
                        Business</span><span class="metric-val">10+ Years</span></div>
                <div class="why-cta"><a href="<?= View::url('booking') ?>"><i class="fa-solid fa-calendar-check"></i>
                        Schedule a Cleaning</a></div>
            </div>
        </div>
    </div>
</section>

<!-- ── TESTIMONIALS ── -->
<section class="testimonials-section" id="reviews">
    <div class="text-center reveal">
        <div class="section-label" style="justify-content:center;">Client Reviews</div>
        <h2 class="section-title">What Our Clients Say</h2>
        <p class="section-sub center">Trusted by thousands of homeowners. Here's what they have to say.</p>
    </div>
    <div class="testimonials-grid reveal">
        <div class="testimonial-card">
            <div class="t-stars">★★★★★</div>
            <p class="t-quote">"Absolutely outstanding. My apartment has never looked this clean. The team arrived on
                time, was professional throughout, and went above and beyond. I've signed up for a monthly plan."</p>
            <div class="t-author">
                <div class="t-avatar">SM</div>
                <div>
                    <div class="t-name">Sarah M.</div>
                    <div class="t-loc"><i class="fa-solid fa-location-dot"
                            style="font-size:.7rem;margin-right:4px;color:var(--blue);"></i>Manhattan, NY</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="t-stars">★★★★★</div>
            <p class="t-quote">"I've used five different cleaning services over the years — BronxHomeServices is far and
                away the best. Booking online took under a minute, the cleaner showed up exactly on time. Exceptional
                value."</p>
            <div class="t-author">
                <div class="t-avatar">JT</div>
                <div>
                    <div class="t-name">James T.</div>
                    <div class="t-loc"><i class="fa-solid fa-location-dot"
                            style="font-size:.7rem;margin-right:4px;color:var(--blue);"></i>Brooklyn, NY</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="t-stars">★★★★★</div>
            <p class="t-quote">"Used BronxHomeServices for a move-out clean and my landlord was genuinely impressed. Got
                my full security deposit back. The deep clean pricing is very fair for the quality you receive."</p>
            <div class="t-author">
                <div class="t-avatar">AK</div>
                <div>
                    <div class="t-name">Amara K.</div>
                    <div class="t-loc"><i class="fa-solid fa-location-dot"
                            style="font-size:.7rem;margin-right:4px;color:var(--blue);"></i>Queens, NY</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── FINAL CTA ── -->
<section class="cta-section">
    <div class="cta-inner reveal">
        <div class="section-label" style="justify-content:center;color:#60A5FA;">
            <span style="width:20px;height:2px;background:#60A5FA;display:inline-block;"></span>
            Get Started Today
        </div>
        <h2 class="section-title">Ready for a Cleaner Home?</h2>
        <p>Book online in under 60 seconds. First-time clients receive 15% off their first clean.</p>
        <a href="<?= View::url('booking') ?>" class="cta-btn">
            <i class="fa-solid fa-calendar-check"></i> Book Your Cleaning Now
        </a>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="logo-text">Bronx<span>HomeServices</span></div>
            <p>Professional home and office cleaning services. Fully insured, background-checked, and satisfaction
                guaranteed on every visit.</p>
        </div>
        <div class="footer-col">
            <h4>Services</h4>
            <ul>
                <li><a href="<?= View::url('booking') ?>">Standard Cleaning</a></li>
                <li><a href="<?= View::url('booking') ?>">Deep Cleaning</a></li>
                <li><a href="<?= View::url('booking') ?>">Move In / Out</a></li>
                <li><a href="<?= View::url('booking') ?>">Office Cleaning</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Press</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="#">Contact</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2026 BronxHomeServices. All rights reserved.</span>
        <div class="social-links">
            <a href="#" class="social-link" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="social-link" aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="social-link" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>

<script src="<?= View::asset('js/home.js') ?>"></script>