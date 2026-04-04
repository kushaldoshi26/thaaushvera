from modules.design import design_ai
from modules.image import image_ai
from modules.business import business_ai
from modules.debug import debug_ai


def route_task(message, data):
    msg = message.lower()

    if "image" in msg or "generate banner" in msg:
        return image_ai(message)

    elif "design" in msg:
        return design_ai(message)

    elif "sales" in msg or "revenue" in msg:
        return business_ai(message, data)

    elif "error" in msg or "bug" in msg:
        return debug_ai(message, data)

    else:
        return {"response": "Specify design, image, business or debug task."}
