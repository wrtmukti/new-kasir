---
name: Alpine Hearth
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f3'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1a1c1c'
  on-surface-variant: '#424843'
  inverse-surface: '#2f3131'
  inverse-on-surface: '#f1f1f1'
  outline: '#727972'
  outline-variant: '#c2c8c0'
  surface-tint: '#466550'
  primary: '#163422'
  on-primary: '#ffffff'
  primary-container: '#2d4b37'
  on-primary-container: '#99baa1'
  inverse-primary: '#adcfb4'
  secondary: '#725a39'
  on-secondary: '#ffffff'
  secondary-container: '#fbdbb0'
  on-secondary-container: '#765f3d'
  tertiary: '#2e2e2e'
  on-tertiary: '#ffffff'
  tertiary-container: '#444444'
  on-tertiary-container: '#b3b1b1'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c8ebd0'
  primary-fixed-dim: '#adcfb4'
  on-primary-fixed: '#022110'
  on-primary-fixed-variant: '#2f4d39'
  secondary-fixed: '#feddb3'
  secondary-fixed-dim: '#e1c299'
  on-secondary-fixed: '#281801'
  on-secondary-fixed-variant: '#584324'
  tertiary-fixed: '#e4e2e1'
  tertiary-fixed-dim: '#c8c6c6'
  on-tertiary-fixed: '#1b1c1c'
  on-tertiary-fixed-variant: '#474747'
  background: '#f9f9f9'
  on-background: '#1a1c1c'
  surface-variant: '#e2e2e2'
typography:
  headline-xl:
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
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Montserrat
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 34px
  headline-md:
    fontFamily: Montserrat
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
  label-bold:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '700'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
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
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
  stack-sm: 12px
  stack-md: 24px
  stack-lg: 48px
---

## Brand & Style

The design system is built for the "Mountain Explorer," balancing the rugged durability of outdoor gear with the warmth of a mountain-side coffee shop. The aesthetic follows a **Modern-Tactile** movement—merging clean, functional layouts with organic textures and physical depth that suggests craftsmanship and reliability.

The personality is adventurous yet cozy. The interface should feel like a well-worn leather map or a solid oak table: grounded, dependable, and inviting. High-quality nature photography and subtle topographic patterns are used to break up digital flat surfaces, grounding the UI in the physical world.

## Colors

The palette is derived from the high-alpine environment. 

*   **Primary (Deep Forest Green):** Used for brand-defining moments, primary actions, and navigation headers. It represents the density of the treeline.
*   **Secondary (Earthy Tan):** Used for highlights, accents, and call-to-action buttons. It provides a warm, organic contrast to the cool greens.
*   **Tertiary (Charcoal Bark):** Used for typography and structural elements where high contrast is needed without the harshness of pure black.
*   **Neutral (Misty Off-White):** The primary background color, providing a breathable, clean canvas that mimics mountain fog.

Use subtle overlays of topographic line art (low opacity Primary or Secondary) on Neutral backgrounds to add depth.

## Typography

This design system utilizes **Montserrat** for all headlines to project strength and stability. The heavy weights and geometric construction provide a "sturdy" feel reminiscent of vintage park signage.

**Manrope** is used for body text and labels to ensure maximum legibility at smaller scales. Its modern, refined character balances the ruggedness of the headlines, ensuring the interface feels contemporary. Use "Label-Bold" for small caps navigation and category tags to maintain an organized, functional hierarchy.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy on desktop (12 columns, 1200px max-width) to maintain a sense of structured composition. On mobile, it transitions to a single-column fluid layout with generous 16px side margins.

Spacing is based on an **8px linear scale**. High-impact sections (like coffee roast profiles or featured gear) should use "Stack-LG" (48px) to create an airy, premium feel. Related content items (like product grids) use "Stack-MD" (24px) for clear association.

## Elevation & Depth

To achieve a "durable" and "tactile" look, the design system avoids ultra-thin lines and flat surfaces.

*   **Depth:** Use 2px "inner-shadow" or bottom-heavy borders on buttons to create a pressed or embossed effect, suggesting physical resilience.
*   **Layering:** Surfaces use **Tonal Layers**. Elements closer to the user are slightly warmer (using Earthy Tan at very low opacities) rather than just being a different shade of grey.
*   **Shadows:** Shadows are rare and intentional. When used, they are "Ambient Shadows"—diffused, low-opacity, and slightly tinted with the Primary Green to feel like natural shadows under a forest canopy.

## Shapes

The shape language is **Rounded (8px)**. This medium radius softens the industrial nature of the typography and colors, making the UI feel approachable and "organic" like rounded river stones.

*   **Standard (rounded):** 0.5rem (8px) for cards, input fields, and standard buttons.
*   **Large (rounded-lg):** 1rem (16px) for major modal containers and image carousels.
*   **Extra Large (rounded-xl):** 1.5rem (24px) for accent decorative elements or "Buy Now" feature cards.

## Components

*   **Buttons:** Primary buttons use the Earthy Tan background with Charcoal Bark text. They feature a 2px bottom border (Primary Green) to simulate thickness and durability.
*   **Chips:** Used for gear categories (e.g., "Brewing," "Apparel"). Use a Misty Off-White background with a 1px solid Primary Green border.
*   **Cards:** Product cards use the Misty Off-White background. High-quality product photography should fill the top half, with a subtle topographic watermark in the bottom corner of the text area.
*   **Input Fields:** Fields are outlined in Charcoal Bark (low opacity) with a 2px focus state in Primary Green. The background is slightly darker than the page background to suggest a "carved out" area.
*   **Lists:** Items are separated by subtle Earthy Tan horizontal rules (1px, 20% opacity).
*   **Badges:** For "New" or "In Stock" alerts, use the Primary Green background with Misty Off-White text, using the "Label-Bold" typography style.