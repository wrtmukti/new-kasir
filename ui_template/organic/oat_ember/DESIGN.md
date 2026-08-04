---
name: Oat & Ember
colors:
  surface: '#fff8f3'
  surface-dim: '#e3d8ce'
  surface-bright: '#fff8f3'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#fdf2e7'
  surface-container: '#f7ece1'
  surface-container-high: '#f1e6dc'
  surface-container-highest: '#ebe1d6'
  on-surface: '#201b15'
  on-surface-variant: '#434840'
  inverse-surface: '#352f29'
  inverse-on-surface: '#faefe4'
  outline: '#737970'
  outline-variant: '#c3c8be'
  surface-tint: '#496546'
  primary: '#476344'
  on-primary: '#ffffff'
  primary-container: '#5f7c5b'
  on-primary-container: '#f7fff1'
  inverse-primary: '#afcfa9'
  secondary: '#745853'
  on-secondary: '#ffffff'
  secondary-container: '#fed7d0'
  on-secondary-container: '#795c57'
  tertiary: '#5d5c56'
  on-tertiary: '#ffffff'
  tertiary-container: '#76746e'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#cbebc3'
  primary-fixed-dim: '#afcfa9'
  on-primary-fixed: '#062108'
  on-primary-fixed-variant: '#324d30'
  secondary-fixed: '#ffdad4'
  secondary-fixed-dim: '#e3beb8'
  on-secondary-fixed: '#2b1613'
  on-secondary-fixed-variant: '#5b403c'
  tertiary-fixed: '#e6e2da'
  tertiary-fixed-dim: '#c9c6bf'
  on-tertiary-fixed: '#1c1c17'
  on-tertiary-fixed-variant: '#484741'
  background: '#fff8f3'
  on-background: '#201b15'
  surface-variant: '#ebe1d6'
typography:
  headline-xl:
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
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
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
  body-sm:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 14px
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
  xl: 48px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
  max-width: 1200px
---

## Brand & Style

The design system is built on the "Scandi-Cafe" aesthetic: a fusion of Scandinavian minimalism and the tactile warmth of a local coffee house. It prioritizes comfort, clarity, and a sense of calm efficiency. The goal is to move away from cold, digital-first interfaces toward an environment that feels organic and hospitable.

The style is characterized by heavy whitespace, soft edges, and a "High-End Minimalist" approach. It avoids aggressive gradients and sharp corners in favor of subtle tonal shifts and generous breathing room. The interface should feel like a well-lit physical space—airy, organized, and inviting.

## Colors

The palette is derived from natural ingredients and materials found in a modern cafe environment.

- **Soft Matcha (Primary):** A muted, organic green used for primary actions and highlights. It suggests freshness and growth without being visually fatiguing.
- **Roasted Espresso (Secondary/Text):** A deep, rich brown used for primary typography and high-contrast elements. It provides a warmer, more sophisticated alternative to pure black.
- **Creamy Oat (Surface):** The foundational background color. It is a warm, off-white neutral that reduces eye strain and provides a soft canvas for content.
- **Steamed Milk (Neutral):** Lighter accents and border colors that provide subtle structure without breaking the minimalist flow.

Functional colors (Success, Warning, Error) should be desaturated to match the earthy tone of the core palette.

## Typography

This design system uses **Manrope** exclusively to maintain a modern yet friendly character. The typeface's geometric qualities provide the "Scandi" precision, while its open apertures ensure high readability and a welcoming feel.

- **Headlines:** Use tight letter-spacing and bold weights to create a strong visual anchor.
- **Body:** Maintain generous line-heights (1.5x minimum) to ensure long-form content feels breathable and easy to digest.
- **Labels:** Use uppercase for smaller labels to differentiate from body text and provide a clean, architectural look to forms and UI metadata.

## Layout & Spacing

The layout philosophy follows a **Fixed-Fluid Hybrid** model. Content is centered within a maximum width of 1200px on desktop to prevent excessive eye travel, while margins scale fluidly on smaller devices.

- **Grid:** A 12-column grid is used for desktop, shifting to a 4-column grid for mobile.
- **Rhythm:** An 8px base unit (softened by 4px increments for tight UI) governs all padding and margins. 
- **Density:** This design system favors low-density layouts. Components should never feel "packed." When in doubt, increase the spacing to the next tier (e.g., from `lg` to `xl`) to maintain the airy Scandi vibe.

## Elevation & Depth

This design system avoids heavy shadows and traditional skeuomorphism. Instead, it uses **Tonal Layering** and **Soft Ambient Shadows**.

- **Surfaces:** Depth is primarily communicated through color shifts. The main background is "Creamy Oat," while "Elevated" surfaces (like cards or menus) use a pure white or a very subtle 2% darker tint of the background.
- **Shadows:** When necessary for functional elevation (e.g., a floating action button or a modal), use a very large blur radius (32px+) with very low opacity (5-8%) using a "Roasted Espresso" tint rather than pure black. This creates a soft "glow" effect rather than a harsh drop shadow.
- **Outlines:** Use 1px solid borders in a slightly darker shade of the surface color for most containers to maintain a crisp, clean definition without the weight of shadows.

## Shapes

The shape language is defined by "Soft" geometry. Every element should feel approachable and safe to touch.

- **Standard Elements:** Buttons, input fields, and small cards use the `rounded-md` (0.5rem) setting.
- **Large Containers:** Content cards, modals, and featured sections use `rounded-lg` (1rem) or `rounded-xl` (1.5rem) to emphasize the friendly, organic nature of the brand.
- **Interactive Indicators:** Small chips or status tags can utilize a "Pill" shape (full radius) to distinguish them from larger structural elements.

## Components

- **Buttons:** Primary buttons use a "Soft Matcha" fill with white or "Roasted Espresso" text. Secondary buttons should be outlined in "Roasted Espresso" with a transparent background. All buttons have a minimum height of 48px to ensure accessibility.
- **Inputs:** Text fields use a subtle "Steamed Milk" border that thickens slightly on focus. Labels sit clearly above the field in the `label-sm` style.
- **Cards:** Cards should have no border-width greater than 1px. They use a flat style or a very soft ambient shadow on hover to indicate interactivity.
- **Chips & Tags:** Use these for categories (e.g., "Medium Roast", "Vegan"). They should be monochromatic (light tint of the primary color) to remain unobtrusive.
- **Lists:** Use generous vertical padding (16px) between list items. Dividers should be 1px and very faint, only appearing if absolutely necessary for legibility.
- **Steppers/Progress:** Use organic, circular shapes and soft green fills to guide the user through workflows without causing anxiety.