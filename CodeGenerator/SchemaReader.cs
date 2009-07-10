using System;
using System.Data;
using System.Collections;

namespace GURUCORE.GForm.CodeGenerator
{
	/// <summary>
	/// Summary description for SchemaReader.
	/// </summary>
	public abstract class SchemaReader
	{
		protected IDbConnection m_oDbConn;
		public SchemaReader()
		{
		}

		public SchemaReader(IDbConnection p_oDbConn)
		{
			m_oDbConn = p_oDbConn;
		}

		public abstract ArrayList GetSchemaObjects();
	}
}
