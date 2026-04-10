from fastapi import FastAPI
from pydantic import BaseModel
import uvicorn

app = FastAPI(title="Aushvera AI Agent")

class QueryRequest(BaseModel):
    query: str

@app.get("/")
def read_root():
    return {"status": "AI Agent is running", "service": "aushvera-ai"}

@app.post("/ask")
def ask_ai(request: QueryRequest):
    # Skeleton response - hook up LLM logic here
    return {
        "success": True, 
        "query": request.query,
        "response": f"AI response for: {request.query}"
    }

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=10000)
