---
name: Metropolis Brew
colors:
  surface: '#f9f9ff'
  surface-dim: '#d3daea'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eefe'
  surface-container-high: '#e2e8f8'
  surface-container-highest: '#dce2f3'
  on-surface: '#151c27'
  on-surface-variant: '#3d4a42'
  inverse-surface: '#2a313d'
  inverse-on-surface: '#ebf1ff'
  outline: '#6d7a72'
  outline-variant: '#bccac0'
  surface-tint: '#006c4a'
  primary: '#006948'
  on-primary: '#ffffff'
  primary-container: '#00855d'
  on-primary-container: '#f5fff7'
  inverse-primary: '#68dba9'
  secondary: '#575e70'
  on-secondary: '#ffffff'
  secondary-container: '#d9dff5'
  on-secondary-container: '#5c6274'
  tertiary: '#5a5c5d'
  on-tertiary: '#ffffff'
  tertiary-container: '#737576'
  on-tertiary-container: '#fcfdfe'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#85f8c4'
  primary-fixed-dim: '#68dba9'
  on-primary-fixed: '#002114'
  on-primary-fixed-variant: '#005137'
  secondary-fixed: '#dce2f7'
  secondary-fixed-dim: '#c0c6db'
  on-secondary-fixed: '#141b2b'
  on-secondary-fixed-variant: '#404758'
  tertiary-fixed: '#e1e3e4'
  tertiary-fixed-dim: '#c5c7c8'
  on-tertiary-fixed: '#191c1d'
  on-tertiary-fixed-variant: '#454748'
  background: '#f9f9ff'
  on-background: '#151c27'
  surface-variant: '#dce2f3'
typography:
  display-lg:
    fontFamily: Manrope
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 40px
  xl: 64px
  container-max: 1280px
  gutter: 24px
---

## Brand & Style

The design system is built on the philosophy of **Precision Commerce**. It targets the high-end urban professional who values efficiency as much as quality. The visual language is ultra-clean and minimalist, evoking the atmosphere of a modern, glass-fronted espresso bar in a metropolitan business district.

The aesthetic prioritizes clarity and architectural structure. By utilizing a "High-End Corporate" style mixed with "Minimalism," the UI remains uncluttered, using intentional whitespace to frame content. The emotional response is one of calm reliability and sophisticated speed—getting a premium product without the friction of a crowded interface.

## Colors

The palette is anchored by a sharp "City Emerald" (#059669), used sparingly for primary actions and brand emphasis to maintain a professional, tech-forward edge. 

- **Primary:** City Emerald (#059669) for key interactions and brand presence.
- **Surface:** A base of crisp white (#FFFFFF) paired with very light cool gray (#F9FAFB) for subtle section differentiation.
- **Text/Deep Neutral:** Slate Black (#111827) for high-legibility typography and icons.
- **Accents:** Thin, elegant borders use a light cool gray (#E5E7EB) to define structure without adding visual weight.

## Typography

This design system utilizes **Manrope** across all levels to achieve a geometric yet approachable feel. The typography follows a strict hierarchy to facilitate rapid scanning. 

Headlines use semi-bold and bold weights with slightly tightened letter-spacing to appear "compact" and architectural. Body text is set with generous line-height to ensure readability against the minimalist background. Labels use an uppercase treatment with increased letter-spacing to denote secondary metadata and technical details, reinforcing the precision of the brand.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy on desktop and a **Fluid Grid** on mobile. 
- **Desktop:** A 12-column grid with a 1280px max-width, 24px gutters, and 40px side margins. 
- **Mobile:** A 4-column grid with 16px margins and 16px gutters.

Spacing is governed by a 4px baseline, but the "Precision Commerce" look is achieved through large, intentional gaps (using `lg` and `xl` tokens) between major sections. This creates a gallery-like feel where products and information are given room to breathe.

## Elevation & Depth

This design system eschews heavy shadows in favor of **Tonal Layers** and **Low-Contrast Outlines**. 
- **Surfaces:** Use `#F9FAFB` to distinguish container areas from the `#FFFFFF` background.
- **Outlines:** Elements are defined by 1px solid borders in a very light cool gray (#E5E7EB).
- **Interactive Depth:** Only the most critical interactive elements (like a primary cart button or a featured product card) may use a highly diffused, 2% opacity black shadow to suggest a subtle "lift" from the page. The overall impression should remain flat and structured.

## Shapes

The shape language is defined by **Subtle Roundness (Soft)**. A consistent 0.25rem (4px) radius is applied to buttons, input fields, and small UI components. Larger containers like product cards or modals use a 0.5rem (8px) radius. This specific level of rounding maintains a "sharp" architectural precision while being just soft enough to feel modern and premium rather than harsh or industrial.

## Components

- **Buttons:** Primary buttons are solid "City Emerald" with white text, using the 4px roundedness. Secondary buttons use a 1px Slate Black border with no fill.
- **Input Fields:** Minimalist containers with a 1px light gray border. On focus, the border transitions to City Emerald. Labels are always positioned above the field using the `label-md` style.
- **Product Cards:** Clean white backgrounds with a subtle 1px border. No shadows. Imagery should be high-contrast and professional, filling the top half of the card.
- **Chips:** Used for coffee categories (e.g., "Single Origin," "Cold Brew"). These are pill-shaped with a light gray background and `label-sm` typography.
- **Lists:** Clean rows separated by 1px horizontal dividers. No icons unless they serve a functional purpose (e.g., a "Remove" button in a cart).
- **Navigation:** A simple, high-clarity top bar with a 1px bottom border. Links use `label-md` for a sophisticated, understated look.