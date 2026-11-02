# TechShop Agent Skills

This bundle provides a starter set of project-level instructions and eight focused Agent Skills for the TechShop Ecommerce repository.

## Contents

- `AGENTS.md` — shared repository instructions (Vietnamese communication, safe workflow, review expectations).
- `.agents/skills/project-context/SKILL.md` — repository discovery and architecture context.
- `.agents/skills/laravel-backend/SKILL.md` — Laravel/PHP backend practices.
- `.agents/skills/vue-frontend/SKILL.md` — Vue/TypeScript frontend practices.
- `.agents/skills/ecommerce-business/SKILL.md` — ecommerce domain invariants and end-to-end flows.
- `.agents/skills/api-integration/SKILL.md` — API contracts, frontend integration and webhooks.
- `.agents/skills/database-design/SKILL.md` — schema, migrations, indexes and query safety.
- `.agents/skills/ui-ux-implementation/SKILL.md` — Stitch-to-Vue implementation.
- `.agents/skills/code-review/SKILL.md` — review checklist and findings format.

## Installation

1. Extract the ZIP into the TechShop repository root.
2. Merge the included `AGENTS.md` with any existing root `AGENTS.md`; do not overwrite project-specific instructions without reviewing them.
3. Keep the `.agents/skills/` directory structure unchanged.
4. Reload/reopen the Antigravity workspace or start a new agent conversation.
5. Check the Agent Skills/Customizations panel to confirm the skills are discovered.

## How to use

Ask naturally in Vietnamese, for example:

> Hãy phân tích luồng thêm sản phẩm vào giỏ hàng. Đọc các file liên quan trước, sử dụng Skills phù hợp, chưa sửa code cho đến khi trình bày được flow hiện tại và các rủi ro.

Or:

> Dùng skill `code-review` để review các thay đổi hiện tại. Chỉ báo cáo lỗi có căn cứ từ source code.

Skills should be selected when relevant; explicitly mention the skill name in your request if you want to direct the agent to a particular workflow.

## Important customization step

This is a safe starter pack based on the known project shape (Laravel/PHP backend, Vue frontend, ecommerce domain, Stitch reference folder). It intentionally tells the agent to inspect exact framework versions, packages, schema, routes and conventions before acting. It does not claim that every ecommerce module listed is already implemented.

After installation, ask the agent to inspect the repository and refine the skills using verified project-specific details. Do not let it modify application source code during that discovery pass.
