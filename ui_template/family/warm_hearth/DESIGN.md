---
name: Warm Hearth
colors:
  surface: '#fff8f5'
  surface-dim: '#e5d7d1'
  surface-bright: '#fff8f5'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#fff1ea'
  surface-container: '#f9ebe4'
  surface-container-high: '#f3e5df'
  surface-container-highest: '#ede0d9'
  on-surface: '#211a16'
  on-surface-variant: '#56423c'
  inverse-surface: '#362f2b'
  inverse-on-surface: '#fceee7'
  outline: '#89726a'
  outline-variant: '#ddc1b7'
  surface-tint: '#9e421d'
  primary: '#9e421d'
  on-primary: '#ffffff'
  primary-container: '#ff8c61'
  on-primary-container: '#752501'
  inverse-primary: '#ffb59b'
  secondary: '#4a6549'
  on-secondary: '#ffffff'
  secondary-container: '#ccebc7'
  on-secondary-container: '#506b4f'
  tertiary: '#615e55'
  on-tertiary: '#ffffff'
  tertiary-container: '#b0aba0'
  on-tertiary-container: '#424037'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbcf'
  primary-fixed-dim: '#ffb59b'
  on-primary-fixed: '#380d00'
  on-primary-fixed-variant: '#7e2b07'
  secondary-fixed: '#ccebc7'
  secondary-fixed-dim: '#b0cfad'
  on-secondary-fixed: '#07200b'
  on-secondary-fixed-variant: '#334d33'
  tertiary-fixed: '#e8e2d6'
  tertiary-fixed-dim: '#cbc6bb'
  on-tertiary-fixed: '#1d1c15'
  on-tertiary-fixed-variant: '#49473e'
  background: '#fff8f5'
  on-background: '#211a16'
  surface-variant: '#ede0d9'
typography:
  display-lg:
    fontFamily: Quicksand
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Quicksand
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Quicksand
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  title-lg:
    fontFamily: Quicksand
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Be Vietnam Pro
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Be Vietnam Pro
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Be Vietnam Pro
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-margin: 24px
  gutter: 16px
  section-gap: 64px
  touch-target-min: 48px
---

## Brand & Style
The design system is built to evoke the warmth of a communal family dinner. It targets families, local diners, and food enthusiasts who value comfort and accessibility. 

The aesthetic is a blend of **Modern Minimalism** and **Tactile Softness**. It prioritizes high legibility and large interactive areas to accommodate users of all ages. The emotional goal is to feel sunny, optimistic, and dependable—like a well-lit kitchen in the morning. Visuals should avoid sharp edges or cold technicality, opting instead for a "pillowy" interface that feels safe and inviting.

## Colors
This design system uses a strictly light-mode palette to maintain a "sunny" atmosphere.

- **Primary (Sunset Orange):** Used for primary calls-to-action, active states, and brand-critical highlights. It is warm and stimulates appetite.
- **Secondary (Gentle Sage):** Used for success states, secondary categories (like vegetarian options), and subtle background accents. It provides a natural, fresh balance to the orange.
- **Tertiary (Soft Cream):** The foundation of the UI. This replaces pure white for all main backgrounds to reduce eye strain and feel more organic.
- **Neutral (Earth Brown):** Used for text and icons. Avoid pure black; this warm charcoal-brown maintains the soft aesthetic while ensuring high contrast for accessibility.

## Typography
The typography strategy pairs the rounded, friendly geometry of **Quicksand** for headlines with the clean, contemporary legibility of **Be Vietnam Pro** for body text.

- **Headlines:** Use Quicksand with a tighter letter-spacing for large displays to emphasize its friendly, "bubbly" character.
- **Body Text:** Be Vietnam Pro provides a neutral, highly readable experience for menus and descriptions.
- **Hierarchy:** Maintain generous vertical rhythm. Titles should never feel cramped against the body text.

## Layout & Spacing
The layout follows a **Fluid Grid** model with generous safe areas.

- **Desktop:** 12-column grid with 24px gutters.
- **Mobile:** 4-column grid with 16px gutters and 24px side margins to ensure content feels "contained" and cozy.
- **Philosophy:** Use "Airy Spacing." Elements should have significant padding to reinforce the relaxed, unhurried vibe of a family meal. No element should be smaller than the 48px touch-target minimum to accommodate children and elderly users.

## Elevation & Depth
Depth in this design system is achieved through **Ambient Shadows** and **Tonal Layering**.

- **Shadows:** Use extremely soft, diffused shadows with a slight color tint derived from the Neutral Earth Brown (e.g., #5C534E at 8% opacity). Shadows should have a large blur radius (16px+) to feel "glowy" rather than "sharp."
- **Layering:** The Soft Cream background acts as the base. Cards and containers use pure white (#FFFFFF) to pop forward, or a very light Sage (#F2F5F1) for grouped secondary information.
- **Interactions:** On hover or tap, elements should lift slightly (increasing shadow blur) to provide a tactile, responsive feel.

## Shapes
The shape language is dominated by **Extra Large Rounded Corners**. 

- **Standard Elements:** Buttons and small input fields use `rounded-lg` (1rem/16px).
- **Containers:** Cards, modals, and image containers use `rounded-xl` (1.5rem/24px) or even `2xl` for large feature sections.
- **Icons:** Should always use a "rounded" stroke cap and join style to match the typography. Avoid sharp 90-degree angles in any decorative or functional graphics.

## Components
- **Buttons:** Primary buttons are Sunset Orange with white text. Use a heavy horizontal padding (24px+) and a height of 56px for a "chunky," tappable feel.
- **Cards:** White backgrounds with `rounded-xl` corners and soft ambient shadows. Used for menu items and featured promotions.
- **Chips:** Used for dietary filters (e.g., "Vegan," "Gluten-Free"). Use Sage Green backgrounds with dark Earth Brown text.
- **Input Fields:** Thick 2px borders in a light Sage or Cream-Grey. Focus states should use the Sunset Orange for the border.
- **Lists:** Use generous 16px vertical padding between items. Use Sage Green bullets or icons to maintain the friendly atmosphere.
- **Feedback:** Success messages use the Sage Green; warnings or errors should use a soft terracotta (avoiding "harsh" reds to maintain the friendly vibe).