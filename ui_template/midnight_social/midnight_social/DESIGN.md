---
name: Midnight Social
colors:
  surface: '#0c1324'
  surface-dim: '#0c1324'
  surface-bright: '#33394c'
  surface-container-lowest: '#070d1f'
  surface-container-low: '#151b2d'
  surface-container: '#191f31'
  surface-container-high: '#23293c'
  surface-container-highest: '#2e3447'
  on-surface: '#dce1fb'
  on-surface-variant: '#ccc3d8'
  inverse-surface: '#dce1fb'
  inverse-on-surface: '#2a3043'
  outline: '#958da1'
  outline-variant: '#4a4455'
  surface-tint: '#d2bbff'
  primary: '#d2bbff'
  on-primary: '#3f008e'
  primary-container: '#7c3aed'
  on-primary-container: '#ede0ff'
  inverse-primary: '#732ee4'
  secondary: '#89ceff'
  on-secondary: '#00344d'
  secondary-container: '#00a2e6'
  on-secondary-container: '#00344e'
  tertiary: '#c3c0ff'
  on-tertiary: '#272377'
  tertiary-container: '#5f5db1'
  on-tertiary-container: '#e6e3ff'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#eaddff'
  primary-fixed-dim: '#d2bbff'
  on-primary-fixed: '#25005a'
  on-primary-fixed-variant: '#5a00c6'
  secondary-fixed: '#c9e6ff'
  secondary-fixed-dim: '#89ceff'
  on-secondary-fixed: '#001e2f'
  on-secondary-fixed-variant: '#004c6e'
  tertiary-fixed: '#e2dfff'
  tertiary-fixed-dim: '#c3c0ff'
  on-tertiary-fixed: '#100563'
  on-tertiary-fixed-variant: '#3e3c8f'
  background: '#0c1324'
  on-background: '#dce1fb'
  surface-variant: '#2e3447'
typography:
  headline-xl:
    fontFamily: Sora
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Sora
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Sora
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 34px
  headline-md:
    fontFamily: Sora
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  container-margin: 20px
  gutter: 16px
---

## Brand & Style
The design system is built around the concept of "Atmospheric Connectivity." It targets an urban audience looking for social "nongkrong" experiences, emphasizing the transition from sunset to late-night social hours. 

The aesthetic is **Modern Glassmorphism** mixed with **High-Contrast Neon**. The interface utilizes deep obsidian surfaces to mimic a moody lounge environment, while interactive elements pulse with vibrant energy. The emotional goal is to make the user feel like they are already inside the venue—relaxed, trendy, and part of an exclusive social circle. Whitespace is used generously but "darkly," creating a sense of sophisticated breathing room.

## Colors
The palette is centered on a "Midnight Social" theme, optimized for low-light environments.

- **Primary (Electric Purple):** #7C3AED. Used for high-priority actions, active states, and brand highlights.
- **Secondary (Cyan Pulse):** #0EA5E9. Used for accents, secondary information, and to provide a "cool" contrast to the purple.
- **Tertiary (Deep Indigo):** #312E81. Used for container backgrounds and subtle borders.
- **Neutral (Obsidian):** #020617. The primary background color. 
- **Surface:** A semi-transparent variation of the indigo is used for glassmorphic cards to maintain depth.
- **Text:** High-purity white (#F8FAFC) for primary content and a muted slate (#94A3B8) for secondary metadata.

## Typography
The typography strategy pairs the technical, geometric personality of **Sora** for headings with the supreme legibility of **Inter** for functional text.

Sora is used for all "hero" moments—venue names, prices, and callouts—giving the app a modern, architectural feel. Inter handles all body copy and interface labels to ensure the UI remains utilitarian and easy to navigate in low-light conditions. Letter spacing is tightened on headlines to feel more "urban" and slightly widened on small labels for clarity.

## Layout & Spacing
This design system utilizes a **4px baseline grid** to ensure mathematical harmony.

- **Mobile:** A 4-column fluid grid with 20px side margins and 16px gutters. Content blocks typically span the full width or 2 columns for grid-style venue browsing.
- **Desktop:** A 12-column fixed grid (max-width 1200px) with 24px gutters.
- **Rhythm:** Use "md" (16px) for internal padding within cards and "lg" (24px) for vertical rhythm between distinct content sections.

## Elevation & Depth
Depth is conveyed through **Atmospheric Layering** rather than traditional shadows:

1.  **Level 0 (Base):** Deep Obsidian (#020617).
2.  **Level 1 (Containers):** Semi-transparent Indigo (#312E81 at 40% opacity) with a 16px background blur (Backdrop Filter).
3.  **Level 2 (Active States):** Subtle inner glows using the primary color (Purple) to simulate neon lighting reflecting off a surface.
4.  **Borders:** Instead of heavy shadows, use 1px "ghost borders" with 10% white opacity to define the edges of glassmorphic elements.

## Shapes
Following the "ROUND_TWELVE" philosophy, the shape language is consistently soft and welcoming to balance the "cool" color palette.

- **Standard Components:** 0.75rem (12px) border radius for buttons and input fields.
- **Feature Cards:** 1rem (16px) for larger venue or event cards.
- **Avatars/Badges:** Fully circular (999px) to contrast against the structured grid.
- **Visual Style:** Avoid sharp edges entirely to maintain the "relaxed lounge" vibe.

## Components
- **Buttons:** Primary buttons use a solid Electric Purple fill with white text. Secondary buttons are "ghost" style with a Cyan Pulse border. All buttons have a subtle 4px outer glow of their respective color when hovered.
- **Glass Cards:** Used for venue listings. Features a 1px border-top (20% white) to catch the "light" and a background blur.
- **Chips/Tags:** Used for "Trending" or "Live Music." Use the Cyan Pulse color with 10% opacity and a high-saturation text color.
- **Input Fields:** Darker than the background (#000000) with a 1px Indigo border. The border transitions to Electric Purple on focus.
- **Status Indicators:** Use a "Pulse" animation for "Live" or "Currently Busy" venue statuses to enhance the dynamic feel.
- **Social Feed Cards:** Use a larger 24px bottom margin to separate different social groups or "spots."