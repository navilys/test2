CREATE TRIGGER APPLICATION_UPDATE 
ON APPLICATION
AFTER UPDATE
AS
BEGIN
  SET NOCOUNT ON;

  DECLARE @appUid nvarchar(32), @appStatus nvarchar(32); 
  
  DECLARE updateCursor CURSOR  FOR SELECT APP_UID, APP_STATUS FROM INSERTED;
  OPEN updateCursor;
  
  FETCH NEXT FROM updateCursor INTO @appUid, @appStatus;

  WHILE @@FETCH_STATUS = 0
  BEGIN
    UPDATE APP_CACHE_VIEW SET APP_STATUS = @appStatus WHERE APP_UID = @appUid;

    FETCH NEXT FROM updateCursor INTO @appUid, @appStatus;
  END 
  CLOSE updateCursor;
  DEALLOCATE updateCursor;
  
END
