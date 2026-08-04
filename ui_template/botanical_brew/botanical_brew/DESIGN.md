---
name: Botanical Brew
colors:
  surface: '#fbf9f4'
  surface-dim: '#dbdad5'
  surface-bright: '#fbf9f4'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3ee'
  surface-container: '#f0eee9'
  surface-container-high: '#eae8e3'
  surface-container-highest: '#e4e2dd'
  on-surface: '#1b1c19'
  on-surface-variant: '#424843'
  inverse-surface: '#30312e'
  inverse-on-surface: '#f2f1ec'
  outline: '#727972'
  outline-variant: '#c2c8c0'
  surface-tint: '#466550'
  primary: '#163422'
  on-primary: '#ffffff'
  primary-container: '#2d4b37'
  on-primary-container: '#99baa1'
  inverse-primary: '#adcfb4'
  secondary: '#4a654f'
  on-secondary: '#ffffff'
  secondary-container: '#c9e7cc'
  on-secondary-container: '#4e6953'
  tertiary: '#312e29'
  on-tertiary: '#ffffff'
  tertiary-container: '#48443e'
  on-tertiary-container: '#b7b1a9'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c8ebd0'
  primary-fixed-dim: '#adcfb4'
  on-primary-fixed: '#022110'
  on-primary-fixed-variant: '#2f4d39'
  secondary-fixed: '#cceacf'
  secondary-fixed-dim: '#b0ceb4'
  on-secondary-fixed: '#062010'
  on-secondary-fixed-variant: '#334d38'
  tertiary-fixed: '#e9e1d9'
  tertiary-fixed-dim: '#ccc5be'
  on-tertiary-fixed: '#1e1b16'
  on-tertiary-fixed-variant: '#4a4640'
  background: '#fbf9f4'
  on-background: '#1b1c19'
  surface-variant: '#e4e2dd'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.2'
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '600'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '500'
    lineHeight: '1.3'
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1.2'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 24px
  margin-desktop: 64px
  margin-mobile: 20px
  section-gap: 80px
---

## Brand & Style
The design system is rooted in the "Greenhouse Sanctuary" aesthetic—a digital extension of a physical space filled with flora and light. The brand personality is relaxing, sustainable, and fresh, targeting an audience that seeks a moment of quietude in their daily routine.

The visual style blends **Minimalism** with **Tactile** organic elements. It prioritizes heavy whitespace to simulate an airy environment, using subtle layering to evoke the depth of a garden. The interface should feel breathable, avoiding cluttered layouts or aggressive calls to action, instead guiding the user with a calm, editorial grace.

## Colors
The palette is derived from natural landscapes and greenhouse materials. 
- **Forest Green (#2D4B37)**: The primary brand color, used for high-emphasis text and primary buttons.
- **Sage Green (#8DAA91)**: Used for secondary actions, iconography, and soft background washes.
- **Warm Oat (#D6CFC7)**: A grounding neutral used for borders, dividers, and structural elements.
- **Cream (#F9F7F2)**: The primary background color to ensure the UI feels warmer and more inviting than pure white.
- **Soft Wood (#A68966)**: An accent reserved for specific interactive highlights or focus states to provide a tactile contrast to the greens.

## Typography
The typography strategy relies on a high-contrast pairing between a sophisticated serif and a functional sans-serif. 

**Playfair Display** is used for all headlines and display text to establish a premium, editorial feel. It should be typeset with slightly tighter letter-spacing in larger sizes to maintain a cohesive visual block.

**Manrope** is the workhorse for body text and functional labels. Its modern, rounded terminals complement the organic theme while ensuring maximum legibility for menu items and descriptions. Use increased line heights (1.6) for body text to maintain the "airy" feel of the brand.

## Layout & Spacing
The layout follows a **fluid grid** model with generous margins to prevent content from feeling cramped. 

- **Desktop**: A 12-column grid with 64px outer margins. Elements should favor centered or offset compositions to mimic an organic, non-linear garden path.
- **Mobile**: A 4-column grid with 20px margins. 
- **Rhythm**: Utilize a strict 8px baseline grid. Section spacing should be aggressive (80px+) to allow the "white space" (Cream) to act as a primary design element.

Avoid sharp edges in layout compositions; use inset containers with large paddings to give elements "room to breathe."

## Elevation & Depth
Depth is conveyed through **Tonal Layers** and **Ambient Shadows** rather than stark borders.

1.  **Surfaces**: Use subtle shifts from Cream to a slightly darker Oat for nested containers.
2.  **Shadows**: Use very soft, diffused shadows with a slight Green tint (e.g., `rgba(45, 75, 55, 0.05)`). The blur radius should be high (20px-40px) to simulate natural, indirect sunlight filtering through leaves.
3.  **Translucency**: For overlays and navigation bars, apply a light backdrop blur (Glassmorphism) with a Cream tint to maintain the "greenhouse glass" effect.

## Shapes
The shape language is defined by **Medium Roundness (ROUND_TWELVE)**. 

- Standard components (buttons, inputs) use a 0.5rem (8px) radius.
- Larger containers and cards use a 1rem (16px) radius.
- Interactive decorative elements (like "Specials" tags) can use a 1.5rem (24px) radius.

This soft rounding removes the clinical feeling of sharp corners, aligning with the organic nature of botanical life.

## Components
- **Buttons**: Primary buttons are solid Forest Green with White text. Secondary buttons use a Sage Green outline with a subtle Oat fill on hover. Use generous horizontal padding (24px+).
- **Cards**: Cards should have no visible border; instead, use a soft ambient shadow and a slightly lighter background than the main page. Include a "leaf" icon or botanical flourish in the corner of featured cards.
- **Input Fields**: Soft Oat background with no border. On focus, transition to a thin Sage Green border with a soft glow.
- **Chips/Filters**: Pill-shaped with a Sage Green tint. Active states should use the Soft Wood accent to stand out.
- **Lists**: Menu lists should use thin Oat dividers. Use the Playfair Display font for item titles and Manrope for prices and descriptions.
- **Botanical Flourishes**: Use SVG-based leaf patterns as subtle background textures (5% opacity) or as decorative separators between major sections.