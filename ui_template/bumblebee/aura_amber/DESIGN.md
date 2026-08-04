---
name: Aura Amber
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f4'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1a1c1c'
  on-surface-variant: '#4d4732'
  inverse-surface: '#2f3131'
  inverse-on-surface: '#f0f1f1'
  outline: '#7e775f'
  outline-variant: '#d0c6ab'
  surface-tint: '#705d00'
  primary: '#705d00'
  on-primary: '#ffffff'
  primary-container: '#ffd700'
  on-primary-container: '#705e00'
  inverse-primary: '#e9c400'
  secondary: '#5f5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e4e2e1'
  on-secondary-container: '#656464'
  tertiary: '#00696f'
  on-tertiary: '#ffffff'
  tertiary-container: '#00f1ff'
  on-tertiary-container: '#006a70'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffe16d'
  primary-fixed-dim: '#e9c400'
  on-primary-fixed: '#221b00'
  on-primary-fixed-variant: '#544600'
  secondary-fixed: '#e4e2e1'
  secondary-fixed-dim: '#c8c6c6'
  on-secondary-fixed: '#1b1c1c'
  on-secondary-fixed-variant: '#474747'
  tertiary-fixed: '#79f5ff'
  tertiary-fixed-dim: '#00dbe8'
  on-tertiary-fixed: '#002022'
  on-tertiary-fixed-variant: '#004f54'
  background: '#f9f9f9'
  on-background: '#1a1c1c'
  surface-variant: '#e2e2e2'
typography:
  display-lg:
    fontFamily: Montserrat
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Montserrat
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
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
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 40px
  xl: 64px
  gutter: 16px
  margin-mobile: 20px
  margin-desktop: auto
---

## Brand & Style

This design system embodies a "Bumblebee" aesthetic—energetic, precise, and warm. It prioritizes a high-contrast, minimalist framework that feels airy and professional. The brand personality is hospitable yet efficient, targeting modern urbanites who value clarity and speed in their morning routine.

The style is **Modern Corporate** with a focus on high-clarity minimalism. It utilizes generous white space to allow the vibrant yellow accents to guide the user's eye toward primary actions. The emotional response is one of optimism and reliability, achieved through a "clean-plate" philosophy where every pixel serves a functional purpose.

## Colors

The palette is anchored by a crisp, sterile **Base White (#FFFFFF)** to ensure maximum "airiness." 

- **Primary (Bumblebee Yellow):** Used exclusively for high-priority calls to action, active states, and brand-defining moments. It should never be used for long-form text.
- **Secondary (Soft Charcoal):** Provides the structural weight. Used for typography, iconography, and secondary buttons to ensure high legibility and a professional groundedness.
- **Neutral:** Pure white backgrounds are paired with subtle grey scales (#F5F5F5) for surface differentiation.

## Typography

The typography strategy uses a "Geometric Hybrid" approach. **Montserrat** is reserved for headlines to provide a bold, architectural feel that mirrors modern cafe signage. **Inter** is utilized for all functional text, body copy, and UI labels to ensure maximum readability and a systematic, clean appearance.

Maintain tight tracking on large headers and generous leading on body text to preserve the "airy" feel. Use Charcoal (#333333) for all primary text; avoid pure black to maintain a sophisticated touch.

## Layout & Spacing

The design system employs a **Fluid-Fixed Hybrid Grid**. For mobile, use a 4-column grid with 20px outside margins. For desktop, the content is centered within a 12-column fixed-width container (1200px) to prevent excessive line lengths.

Spacing follows an 8px rhythmic scale. To achieve the "generous whitespace" requirement, vertical sections should favor `lg` (40px) or `xl` (64px) padding. Components within a group should use `sm` (12px) spacing to maintain clear relationships.

## Elevation & Depth

Hierarchy is established through **Ambient Shadows** and tonal layering rather than heavy borders.

- **Level 0 (Base):** Pure White (#FFFFFF).
- **Level 1 (Cards/Floating Elements):** Subtle, highly diffused shadows. Use a 10% opacity Charcoal (#333333) shadow with a large blur radius (16px) and a slight Y-offset (4px).
- **Interactive States:** On hover or press, the shadow depth should increase, or the element should lift (Y-offset decrease) to provide tactile feedback.
- **Glassmorphism:** Use sparingly for navigation bars or floating order summaries, utilizing a 20px background blur and a 40% opacity white fill to maintain the airy aesthetic.

## Shapes

The design system uses a **Rounded (Level 2)** shape language, specifically targeting an 8px (0.5rem) base radius. This softens the high-contrast color palette, making the "Bumblebee" aesthetic feel friendly and approachable rather than aggressive.

- **Buttons & Inputs:** 8px radius.
- **Large Cards:** 16px (1rem) radius.
- **Profile Avatars/Icons:** Circular (pill-shaped) to contrast against the geometric grid.

## Components

- **Buttons:** Primary buttons use the Bumblebee Yellow (#FFD700) with Charcoal (#333333) text. They should have no border. Secondary buttons use a Charcoal outline (1px) with transparent backgrounds.
- **Chips:** Used for dietary filters (e.g., "Vegan," "Oat Milk"). Use a light grey fill (#F5F5F5) with Charcoal text, switching to Yellow fill when selected.
- **Input Fields:** Use a light grey background (#F5F5F5) with an 8px radius. On focus, the border transitions to 2px Bumblebee Yellow.
- **Cards:** Product cards use a white background and Level 1 ambient shadows. Images should be top-aligned with the 8px corner radius applied to the container.
- **Lists:** Clean, borderless rows separated by subtle 1px dividers (#EEEEEE). Ensure generous 16px vertical padding for each list item to prevent a cramped feel.
- **Order Tracker:** A specialized component using a vertical progress line in Charcoal with Yellow nodes to indicate status.