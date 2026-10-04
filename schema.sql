-- YOGA'S PARKING SYSTEM
-- Supabase PostgreSQL database

CREATE TABLE IF NOT EXISTS public.park1 (
    carno INTEGER PRIMARY KEY,
    carn VARCHAR(100) NOT NULL,
    caro VARCHAR(100) NOT NULL,
    charge NUMERIC(10,2) NOT NULL CHECK (charge >= 0)
);

-- Enable Row Level Security.
ALTER TABLE public.park1 ENABLE ROW LEVEL SECURITY;

-- IMPORTANT:
-- This project uses the SUPABASE_SERVICE_ROLE_KEY on the server.
-- The service role bypasses RLS.
-- Do NOT expose the service role key in browser JavaScript.

-- Optional test data:
-- INSERT INTO public.park1 (carno, carn, caro, charge)
-- VALUES
-- (101, 'BMW', 'Yoganathan', 100.00),
-- (102, 'Audi', 'Karthik', 150.00);
