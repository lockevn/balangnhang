using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for monthly_subscriptionDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("money_monthly_subscription","id",true)]
	 public sealed partial class monthly_subscriptionDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string ENDTIME_FLD = "endtime";
		public const string DESC_FLD = "desc";

		private int m_nid;
		private int m_nu;
		private int m_nendtime;
		private int m_ndesc;

		public monthly_subscriptionDTO()
		{
		}
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("u",false,false)]
		public int u
		{
			get
			{
				return m_nu;
			}
			set
			{
				m_htIsNull["u"] = false;
				m_nu = value;
			}
		}
		
		[PersistenceProperty("endtime",false,false)]
		public int endtime
		{
			get
			{
				return m_nendtime;
			}
			set
			{
				m_htIsNull["endtime"] = false;
				m_nendtime = value;
			}
		}
		
		[PersistenceProperty("desc",false,false)]
		public int desc
		{
			get
			{
				return m_ndesc;
			}
			set
			{
				m_htIsNull["desc"] = false;
				m_ndesc = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
