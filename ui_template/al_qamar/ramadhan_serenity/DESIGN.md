---
name: Ramadhan Serenity
colors:
  surface: '#121411'
  surface-dim: '#121411'
  surface-bright: '#383a36'
  surface-container-lowest: '#0d0f0c'
  surface-container-low: '#1a1c19'
  surface-container: '#1e201d'
  surface-container-high: '#292b27'
  surface-container-highest: '#333532'
  on-surface: '#e3e3de'
  on-surface-variant: '#bfc9c3'
  inverse-surface: '#e3e3de'
  inverse-on-surface: '#2f312e'
  outline: '#89938d'
  outline-variant: '#404944'
  surface-tint: '#95d3ba'
  primary: '#95d3ba'
  on-primary: '#003829'
  primary-container: '#064e3b'
  on-primary-container: '#80bea6'
  inverse-primary: '#2b6954'
  secondary: '#e9c349'
  on-secondary: '#3c2f00'
  secondary-container: '#af8d11'
  on-secondary-container: '#342800'
  tertiary: '#c8c6c3'
  on-tertiary: '#30312e'
  tertiary-container: '#444442'
  on-tertiary-container: '#b2b1ae'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#b0f0d6'
  primary-fixed-dim: '#95d3ba'
  on-primary-fixed: '#002117'
  on-primary-fixed-variant: '#0b513d'
  secondary-fixed: '#ffe088'
  secondary-fixed-dim: '#e9c349'
  on-secondary-fixed: '#241a00'
  on-secondary-fixed-variant: '#574500'
  tertiary-fixed: '#e4e2de'
  tertiary-fixed-dim: '#c8c6c3'
  on-tertiary-fixed: '#1b1c1a'
  on-tertiary-fixed-variant: '#474744'
  background: '#121411'
  on-background: '#e3e3de'
  surface-variant: '#333532'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
  title-md:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.1em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-padding-mobile: 20px
  container-padding-desktop: 64px
  gutter: 16px
  section-gap: 48px
---

## Brand & Style
The design system is built on the pillars of peace, community, and premium hospitality. It bridges the gap between traditional Islamic heritage and modern digital convenience, specifically tailored for a high-end cafe and restaurant experience during the holy month.

The style is **Modern Corporate with Tonal Layering**, utilizing a rich, dark-mode default to mimic the serene "twilight" hours. It employs subtle **Glassmorphism** to represent the ethereal nature of the season and **Minimalist** layouts to ensure the focus remains on high-quality food photography and community gatherings. The emotional response should be one of calm, invitation, and spiritual elegance.

## Colors
This design system uses a palette inspired by the transition from sunset to the night prayer.

- **Primary (Twilight Emerald):** A deep, saturated green used for surfaces and primary brand moments. It symbolizes growth and peace.
- **Secondary (Crescent Gold):** Used for accents, primary actions, and highlights. It represents the guiding light of the crescent moon.
- **Tertiary (Warm Ivory):** Reserved for high-contrast typography and subtle dividers on dark backgrounds.
- **Neutral (Deep Onyx):** A near-black green-tinted neutral used for the base background to provide depth and reduce eye strain during evening use.

Apply a 5% opacity Arabesque pattern (geometric stars and interlacing lines) over the Primary and Neutral backgrounds to add texture without distracting from content.

## Typography
The typography strategy pairings a high-contrast Serif for "moments of reflection" (titles, headers) with a highly legible Sans-Serif for "utility" (menus, descriptions, prices).

- **Headlines:** Use Playfair Display to evoke a sense of tradition and luxury.
- **Body & Labels:** Use Manrope for its clean, geometric construction which balances the decorative nature of the serif.
- **Hierarchy:** Ensure a clear distinction between the "Storytelling" (Serif) and the "Ordering/Action" (Sans-Serif) elements.

## Layout & Spacing
The layout follows a **Fluid Grid** model with generous white space (or "dark space") to maintain a premium feel. 

- **Mobile:** 4-column grid with 20px side margins. Elements should use vertical stacking to emphasize food photography.
- **Desktop:** 12-column grid with a maximum content width of 1200px. 
- **Rhythm:** Use an 8px base unit. Component internal padding should favor larger horizontal values (e.g., 16px top/bottom, 24px left/right) to create a "wide" and relaxed visual breath.

## Elevation & Depth
Elevation is achieved through **Tonal Layers** and **Ambient Shadows** rather than harsh black shadows.

1. **Base:** Neutral Deep Onyx (#1A1C19).
2. **Surface:** Primary Emerald (#064E3B) at 40% opacity with a 20px backdrop blur (Glassmorphism).
3. **Overlay:** Secondary Gold (#D4AF37) used sparingly for borders on active elements to signify focus.
4. **Shadows:** Use diffused, low-opacity shadows with a slight emerald tint (e.g., `rgba(6, 78, 59, 0.3)`) to make cards appear to float gently above the background.

## Shapes
The shape language is sophisticated and soft. 

- **Containers:** Use `rounded-lg` (1rem) for most cards and input fields to convey friendliness.
- **Action Elements:** Buttons and Chips should use `rounded-xl` (1.5rem) or full pill-shapes to create a soft, tactile interface.
- **Visual Accents:** Incorporate subtle arched shapes (mimicking Mihrab or pointed arches) in image masks or header backgrounds to reinforce the cultural narrative.

## Components
- **Buttons:** Primary buttons use a Crescent Gold gradient fill with dark text. Secondary buttons use a ghost style with a 1px Gold border.
- **Cards:** Use the Glassmorphic emerald surface. Images inside cards should have a subtle 4px inner glow to separate them from the dark container.
- **Chips:** Used for dietary tags (Halal, Vegan, etc.). These should be pill-shaped with a Primary Emerald background and Tertiary Ivory text.
- **Input Fields:** Bottom-aligned labels with a simple gold underline or a soft-rounded container with a subtle 5% Ivory fill.
- **Lists:** Menu items should be separated by 1px Ivory dividers at 10% opacity. Prices should always be set in Manrope Bold.
- **Prayer/Iftar Timer:** A specialized component featuring a circular progress ring in Gold, centered on the screen or pinned to the header, using Playfair Display for the countdown digits.